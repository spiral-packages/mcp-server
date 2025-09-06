<?php

declare(strict_types=1);

namespace Spiral\McpServer\Entrypoint;

use Mcp\Server\Contracts\ServerTransportInterface;
use Mcp\Server\Server;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;
use Spiral\Exceptions\ExceptionHandlerInterface;

#[AsCommand(
    name: 'mcp',
    description: 'Start MCP server',
)]
final class McpCommand extends Command
{
    public function __invoke(
        Server $server,
        ServerTransportInterface $transport,
        ExceptionHandlerInterface $errorHandler,
    ): void {
        try {
            $server->listen($transport);
        } catch (\Throwable $e) {
            $errorHandler->report($e);
        }
    }
}
