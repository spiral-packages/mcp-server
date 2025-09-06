<?php

declare(strict_types=1);

namespace Spiral\McpServer;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareRepositoryInterface
{
    /**
     * @return MiddlewareInterface[]
     */
    public function all(): array;
}
