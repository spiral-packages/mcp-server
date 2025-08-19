<?php

declare(strict_types=1);

namespace Spiral\McpServer;

/**
 * @psalm-import-type TCallable from MiddlewareManager
 */
interface MiddlewareRegistryInterface
{
    /**
     * @param TCallable $middleware
     */
    public function register(callable $middleware): void;
}
