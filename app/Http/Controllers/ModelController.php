<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Relay\Facades\Relay;
use Prism\Prism\ValueObjects\Media\Image;
use Illuminate\Support\Facades\Validator;
use Imagick;
use Spatie\PdfToText\Pdf;

class ModelController extends Controller
{
    public function textGeneration()
    {
        $llmModel = 'llama3.2';

        return view('chatbot.chat', compact('llmModel'));
    }

    public function summaryGeneration()
    {
        $llmModel = 'llama3.2';

        return view('summarybot.summary', compact('llmModel'));
    }

    public function mediaGeneration()
    {
        $llmModel = 'granite3.2-vision | llama3.2';

        return view('mediaAnalysis.media-analysis', compact('llmModel'));
    }

    public function chatBot(Request $request)
    {
        $userQuery = $request->input('query');
        $llmModel = 'ai/llama3.2';

        $systemPrompt = <<<PROMPT
            You are a helpful chatbot.
            - Use the `latest-info-pull-tool` ONLY when the user asks for any information like news and any current updates.
            - For any questions that is not required the news API, DO NOT call the tool — just respond directly.
        PROMPT;

        $response = Prism::text()
            ->using(Provider::OpenRouter, $llmModel)
            ->withClientOptions([
                'timeout' => 180,
                'connect_timeout' => 5,
            ])
            ->withClientRetry(2, 500)
            // ->withMaxSteps(2)
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($userQuery)
            // ->withTools(Relay::tools('latest-info-pull-tool'))
            ->asText();

        return response()->json([
            'response' => $response->text ?? '',
        ]);
    }

    public function summaryBot(Request $request)
    {
        $userQuery = $request->input('query');
        $llmModel = 'ai/llama3.2';

        // with system prompts, we can change the response behavior of the model
        $systemPrompts = [
            'general' => "You are an expert summarizer AI.
                Rules:
                - Always summarize text clearly and concisely.
                - Keep the output between 3 to 5 bullet points.
                - Do not add extra commentary or opinions.
                - Always capture the key facts only.",
        ];

        // Pick system prompt
        $systemPrompt = $systemPrompts['general'];

        $startedAt = microtime(true);
        $prompt = 'Summarize this: '.$userQuery;

        $response = Prism::text()
            ->using(Provider::OpenRouter, $llmModel)
            ->withClientOptions([
                'timeout' => 180,
                'connect_timeout' => 5,
            ])
            ->withClientRetry(2, 500)
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($prompt)
            ->asText();

        $endedAt = microtime(true);
        $durationSeconds = round(($endedAt - $startedAt), 3);

        $text = $response->text;

        $promptTokens = $this->estimateTokens($prompt);
        $completionTokens = $this->estimateTokens($text);
        $totalTokens = $promptTokens + $completionTokens;

        return response()->json([
            'response' => $text,
            'duration_s' => $durationSeconds,
            'total_tokens' => $totalTokens,
        ]);
    }

    public function mediaAnalysis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:30240',
            'pdf_with_media' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $userQuestion = $request->input('question');
        $llmModel = 'granite3.2-vision';
        $context = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'pdf') {
                $pdfPath = $file->getRealPath();

                if($request->input('pdf_with_media')){
                    $imagick = new Imagick();
                    $imagick->setResolution(150, 150); // adjust DPI
                    $imagick->readImage($pdfPath);

                    foreach ($imagick as $i => $page) {
                        $page->setImageFormat('png');
                        $tmpFile = sys_get_temp_dir() . '/page_' . $i . '.png';
                        $page->writeImage($tmpFile);

                        $context[] = Image::fromLocalPath($tmpFile);

                        @unlink($tmpFile);
                    }

                    $imagick->clear();
                    $imagick->destroy();
                }else{
                    $pdfText = Pdf::getText($pdfPath);

                    if (!empty(trim($pdfText))) {
                        $textContext = $pdfText;
                        $llmModel = 'ai/llama3.2';
                    }
                }

            } elseif (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $filePath = $request->file('file')->getRealPath();
                $context[] = Image::fromLocalPath($filePath);
            }
        }

        $startedAt = microtime(true);
        $prompt = "Answer the user's question based on this file.\nQuestion: {$userQuestion}";
        $systemPrompt = "You answer questions based on provided context. Give response well structured.";

        if (isset($textContext) && !empty($textContext)) {
            $finalPrompt = "Answer the user's question based on this file.\nContext:\n{$textContext}\n\nQuestion: {$userQuestion}";
            $contextData = [];
        } else {
            $finalPrompt = "Answer the user's question based on this file.\nQuestion: {$userQuestion}";
            $contextData = $context;
        }

        $response = Prism::text()
            ->using(Provider::OpenRouter, $llmModel)
            ->withClientOptions([
                'timeout' => 360,
                'connect_timeout' => 5,
            ])
            ->withClientRetry(2, 500)
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($finalPrompt, $contextData)
            ->asText();

        $endedAt = microtime(true);
        $durationSeconds = round(($endedAt - $startedAt), 3);

        $text = $response->text ?? '';

        $promptTokens = $this->estimateTokens($prompt);
        $completionTokens = $this->estimateTokens($text);
        $totalTokens = $promptTokens + $completionTokens;

        return response()->json([
            'response' => $text,
            'duration_s' => $durationSeconds,
            'total_tokens' => $totalTokens,
        ]);
    }

    private function estimateTokens(string $text): int
    {
        // Simple heuristic: ~4 characters per token (English average)
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($text, 'UTF-8');
        } else {
            $length = strlen($text);
        }

        return (int) max(1, ceil($length / 4));
    }
}
