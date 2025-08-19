<?php

declare(strict_types=1);

namespace Spiral\McpServer\Attribute;

/**
 * If true, the tool may perform destructive updates to its environment.
 * If false, the tool performs only additive updates.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class IsDestructive
{
    public function __construct(
        public bool $destructive = true,
    ) {}
}
