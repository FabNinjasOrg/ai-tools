<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Illuminate\Support\Facades\Http;

class LatestInfoPullTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Fetches the latest news, updates, or current information for a given query.

        - Use this tool **only when the user asks for current events, breaking news, today’s updates, or related information**.
        - The tool accepts a `query` parameter containing the full user request and an optional `max_results` parameter to limit the number of articles returned.
        - Returns structured results in Markdown format, including clickable links, source names, and publication dates.
        - For all other questions not related to current information, this tool **should not be called**.
        - If this tool do not return any relevant information, respond with "No relevant information found.
    MARKDOWN;

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Query for latest information')->required(),
            'max_results' => $schema->integer()->description('Maximum number of articles to fetch')->default(5),
        ];
    }

    public function handle(Request $request): Response
    {
        $query = $request->get('query');
        $maxResults = $request->get('max_results', 5);

        // Fetch news articles
        $news = $this->fetchLatestNews($query, $maxResults);

        // Format each article as Markdown with clickable link
        $formatted = array_map(function($a){
            $url = $a['url'] ?: '#';
            return "**{$a['title']}** - {$a['source']}  \nPublished: {$a['publishedAt']}  \n[Read more]({$url})\n";
        }, $news);

        return Response::text(implode("\n", $formatted));
    }

    protected function fetchLatestNews(string $query, int $maxResults = 5): array
    {
        $apiKey = env('NEWSAPI_KEY'); // Store your key in .env

        $response = Http::get('https://newsapi.org/v2/everything', [
            'q' => $query,
            'apiKey' => $apiKey,
            'pageSize' => $maxResults,
            'sortBy' => 'popularity',
        ]);

        if ($response->failed()) {
            return [["title" => "Failed to fetch news.", "url" => "", "source" => "", "publishedAt" => ""]];
        }

        $articles = $response->json()['articles'] ?? [];
        return array_map(function($a){
            return [
                'title' => $a['title'] ?? '',
                'description' => $a['description'] ?? '',
                'url' => $a['url'] ?? '',
                'publishedAt' => $a['publishedAt'] ?? '',
                'source' => $a['source']['name'] ?? '',
            ];
        }, $articles);
    }
}
