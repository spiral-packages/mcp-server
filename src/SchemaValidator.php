<?php

declare(strict_types=1);

namespace Spiral\McpServer;

/**
 * @internal
 * TODO remove this class when the schema validation fixed
 * @see https://github.com/php-mcp/server/issues/63
 */
final class SchemaValidator extends \PhpMcp\Server\Utils\SchemaValidator
{
    public function validateAgainstJsonSchema(mixed $data, object|array $schema): array
    {
        return [];
    }
}
