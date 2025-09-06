<?php

declare(strict_types=1);

namespace Spiral\McpServer;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareRegistryInterface
{
    public function register(MiddlewareInterface $middleware): void;
}
