<?php

declare(strict_types=1);

namespace Spiral\McpServer\Discovery;

use Spiral\McpServer\Attribute;
use PhpMcp\Schema\ToolAnnotations;
use Spiral\Attributes\ReaderInterface;

/**
 * @internal
 */
final readonly class AttributesParser
{
    public function __construct(
        private ReaderInterface $reader,
    ) {}

    public function parseToolAttribute(\ReflectionClass $class): ?Attribute\Tool
    {
        return $this->reader->firstClassMetadata($class, Attribute\Tool::class);
    }

    public function parseAnnotationAttributes(\ReflectionClass $class): ?ToolAnnotations
    {
        $readOnlyHint = $this->parseReadOnlyAttribute($class);
        $destructiveHint = $this->parseDestructiveAttribute($class);
        $idempotentHint = $this->parseIdempotentAttribute($class);
        $openWorldHint = $this->parseOpenWorldAttribute($class);

        // Only create ToolAnnotations if at least one attribute was found
        if ($readOnlyHint === null && $destructiveHint === null &&
            $idempotentHint === null && $openWorldHint === null) {
            return null;
        }

        return ToolAnnotations::make(
            readOnlyHint: $readOnlyHint,
            destructiveHint: $destructiveHint,
            idempotentHint: $idempotentHint,
            openWorldHint: $openWorldHint,
        );
    }

    private function parseReadOnlyAttribute(\ReflectionClass $class): ?bool
    {
        $attribute = $this->reader->firstClassMetadata($class, Attribute\IsReadonly::class);
        return $attribute?->readOnlyHint;
    }

    private function parseDestructiveAttribute(\ReflectionClass $class): ?bool
    {
        $attribute = $this->reader->firstClassMetadata($class, Attribute\IsDestructive::class);
        return $attribute?->destructive;
    }

    private function parseIdempotentAttribute(\ReflectionClass $class): ?bool
    {
        $attribute = $this->reader->firstClassMetadata($class, Attribute\IsIdempotent::class);
        return $attribute?->idempotent;
    }

    private function parseOpenWorldAttribute(\ReflectionClass $class): ?bool
    {
        $attribute = $this->reader->firstClassMetadata($class, Attribute\IsOpenWorld::class);
        return $attribute?->openWorld;
    }
}
