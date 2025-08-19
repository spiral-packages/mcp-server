<?php

declare(strict_types=1);

namespace Spiral\McpServer;

/**
 * @psalm-import-type TCallable from MiddlewareManager
 */
interface MiddlewareRepositoryInterface
{
    /**
     * @return TCallable[]
     */
    public function all(): array;
}
