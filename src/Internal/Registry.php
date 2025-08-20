<?php

declare(strict_types=1);

namespace Spiral\McpServer\Internal;

use PhpMcp\Schema\Tool;

/**
 * @internal
 *  TODO: Remove this class when RegisteredTool is used Tool handler.
 * @see https://github.com/php-mcp/server/issues/62
 */
final class Registry extends \PhpMcp\Server\Registry
{
    /** @var array<string, RegisteredTool> */
    private array $tools = [];

    public function registerTool(Tool $tool, callable|array|string $handler, bool $isManual = false): void
    {
        $this->tools[$tool->name] = new RegisteredTool($tool, $handler, $isManual);
    }

    public function getTool(string $name): ?RegisteredTool
    {
        return $this->tools[$name] ?? null;
    }

    public function getTools(): array
    {
        return \array_map(static fn($tool) => $tool->schema, $this->tools);
    }
}
