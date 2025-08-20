<?php

declare(strict_types=1);

namespace Spiral\McpServer;

interface SchemaMapperInterface
{
    /**
     * @param class-string $class
     */
    public function toJsonSchema(string $class): array;

    /**
     * @template T of object
     * @param class-string<T>|null $class
     * @return T
     */
    public function toObject(string $json, ?string $class = null): object;
}
