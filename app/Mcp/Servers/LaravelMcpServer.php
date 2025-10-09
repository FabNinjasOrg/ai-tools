<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\LatestInfoPullTool;
use Laravel\Mcp\Server;

class LaravelMcpServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravel AI Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Pull latest information and give response based on user query.
    MARKDOWN;

    // important part to declare
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        LatestInfoPullTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
