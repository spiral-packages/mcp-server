<?php

declare(strict_types=1);

namespace Spiral\McpServer\Attribute;

/**
 * If true, this tool may interact with an "open world" of external entities.
 * If false, the tool's domain of interaction is closed.
 * For example, the world of a web search tool is open, whereas that of a memory tool is not.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class IsOpenWorld
{
    public function __construct(
        public bool $openWorld = true,
    ) {}
}
