<?php

declare(strict_types=1);

namespace Spiral\McpServer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Core\Attribute\Singleton;

/**
 * @internal
 *
 * @template TCallable of callable(ServerRequestInterface, callable): ResponseInterface
 * TODO: Update middleware type when PSR-15 is implemented.
 * @see https://github.com/php-mcp/server/issues/64
 */
#[Singleton]
final class MiddlewareManager implements MiddlewareRepositoryInterface, MiddlewareRegistryInterface
{
    /** @var TCallable[] */
    private array $middlewares = [];

    public function register(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function all(): array
    {
        return $this->middlewares;
    }
}
