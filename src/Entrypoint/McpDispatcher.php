<?php

declare(strict_types=1);

namespace Spiral\McpServer\Entrypoint;

use Mcp\Server\Contracts\ServerTransportInterface;
use Mcp\Server\Server;
use Psr\Container\ContainerInterface;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Exceptions\ExceptionHandlerInterface;

/**
 * @internal
 */
final readonly class McpDispatcher implements DispatcherInterface
{
    public function __construct(
        private ContainerInterface $container,
        private ExceptionHandlerInterface $errorHandler,
    ) {}

    public static function canServe(EnvironmentInterface $env): bool
    {
        return $env->get('SAPI', 'cli') === 'mcp';
    }

    public function serve(): void
    {
        /** @var Server $server */
        $server = $this->container->get(Server::class);
        $transport = $this->container->get(ServerTransportInterface::class);

        try {
            $server->listen($transport);
        } catch (\Throwable $e) {
            $this->errorHandler->report($e);
        }
    }
}
