<?php

use App\Mcp\Servers\LaravelMcpServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('mcp/latest-info-pull', LaravelMcpServer::class);

Mcp::local('latest-info-pull', LaravelMcpServer::class);
