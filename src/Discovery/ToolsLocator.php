<?php

declare(strict_types=1);

namespace Spiral\McpServer\Discovery;

use Mcp\Server\Contracts\ReferenceRegistryInterface;
use Spiral\McpServer\Attribute;
use Spiral\Tokenizer\Attribute\TargetAttribute;
use Spiral\Tokenizer\TokenizationListenerInterface;

/**
 * @internal
 */
#[TargetAttribute(Attribute\Tool::class)]
final readonly class ToolsLocator implements TokenizationListenerInterface
{
    public function __construct(
        private ReferenceRegistryInterface $registry,
        private AttributesParser $attributesParser,
        private ToolFactory $toolFactory,
    ) {}

    public function listen(\ReflectionClass $class): void
    {
        // Skip abstract classes and interfaces
        if (!$class->isInstantiable()) {
            return;
        }

        $toolAttribute = $this->attributesParser->parseToolAttribute($class);
        if ($toolAttribute === null) {
            return;
        }

        $annotations = $this->attributesParser->parseAnnotationAttributes($class);
        $tool = $this->toolFactory->createTool($class, $toolAttribute, $annotations);
        $handler = $this->toolFactory->createHandler($class);

        $this->registry->registerTool($tool, $handler);
    }

    public function finalize(): void {}
}
