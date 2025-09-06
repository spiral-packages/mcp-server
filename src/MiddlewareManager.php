<?php

declare(strict_types=1);

namespace Spiral\McpServer;

use Psr\Http\Server\MiddlewareInterface;
use Spiral\Core\Attribute\Singleton;

/**
 * @internal
 */
#[Singleton]
final class MiddlewareManager implements MiddlewareRepositoryInterface, MiddlewareRegistryInterface
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function register(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function all(): array
    {
        return $this->middlewares;
    }
}
