<?php

declare(strict_types=1);

namespace Spiral\McpServer\Attribute;

/**
 * If true, the tool does not modify its environment.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class IsReadonly
{
    public function __construct(
        public bool $readOnlyHint = true,
    ) {}
}
