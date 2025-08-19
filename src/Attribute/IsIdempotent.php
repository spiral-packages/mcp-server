<?php

declare(strict_types=1);

namespace Spiral\McpServer\Attribute;

/**
 * If true, calling the tool repeatedly with the same arguments will have no additional effect on the its environment.
 * (This property is meaningful only when `readOnlyHint == false`)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class IsIdempotent
{
    public function __construct(
        public bool $idempotent = true,
    ) {}
}
