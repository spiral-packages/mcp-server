<?php

declare(strict_types=1);

namespace Spiral\McpServer\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Tool
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}
}
