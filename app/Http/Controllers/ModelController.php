<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class ModelController extends Controller
{
    public function textGeneration()
    {
        $llmModel = 'gemma3:1b';

        return view('chatbot.chat', compact('llmModel'));
    }

    public function summaryGeneration()
    {
        $llmModel = 'gemma3:1b';

        return view('summarybot.summary', compact('llmModel'));
    }

    public function chatBot(Request $request)
    {
        $userQuery = $request->input('query');
        $llmModel = 'gemma3:1b';

        $response = Prism::text()
            ->using(Provider::Ollama, $llmModel)
            ->withPrompt($userQuery)
            ->asText();

        $text = $response->text;

        return response()->json(['response' => $text]);
    }

    public function summaryBot(Request $request)
    {
        $userQuery = $request->input('query');
        $llmModel = 'gemma3:1b';

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
            ->using(Provider::Ollama, $llmModel)
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
