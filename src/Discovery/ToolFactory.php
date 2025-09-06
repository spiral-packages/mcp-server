<?php

declare(strict_types=1);

namespace Spiral\McpServer\Discovery;

use Mcp\Server\Contracts\HandlerInterface;
use Spiral\McpServer\Attribute;
use PhpMcp\Schema\Tool;
use PhpMcp\Schema\ToolAnnotations;
use Spiral\Core\FactoryInterface;
use Spiral\McpServer\SchemaMapperInterface;

/**
 * @internal
 */
final readonly class ToolFactory
{
    public function __construct(
        private FactoryInterface $factory,
        private SchemaMapperInterface $schemaMapper,
    ) {}

    public function createTool(
        \ReflectionClass $class,
        Attribute\Tool $toolAttribute,
        ?ToolAnnotations $annotations = null,
    ): Tool {
        $this->validateToolClass($class);

        $method = $class->getMethod('__invoke');
        $this->assertHandlerMethodIsPublic($method);

        $name = $toolAttribute->name;
        $description = $toolAttribute->description;
        [$_, $inputSchema] = $this->findSchema($method);

        return Tool::make($name, $inputSchema, $description, $annotations);
    }

    public function createHandler(\ReflectionClass $class): HandlerInterface
    {
        $method = $class->getMethod('__invoke');
        [$schemaClass, $_] = $this->findSchema($method);

        return new ClassHandler(
            factory: $this->factory,
            schemaMapper: $this->schemaMapper,
            class: $class,
            schemaClass: $schemaClass,
        );
    }

    private function validateToolClass(\ReflectionClass $class): void
    {
        if (!$class->isInstantiable()) {
            throw new \InvalidArgumentException(
                \sprintf('Class %s must be instantiable.', $class->getName()),
            );
        }

        if (!$class->hasMethod('__invoke')) {
            throw new \InvalidArgumentException(
                \sprintf('Class %s must have __invoke method.', $class->getName()),
            );
        }
    }

    private function assertHandlerMethodIsPublic(\ReflectionMethod $method): void
    {
        if (!$method->isPublic()) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Handler method %s:%s should be public.',
                    $method->getDeclaringClass()->getName(),
                    $method->getName(),
                ),
            );
        }
    }

    private function findSchema(\ReflectionMethod $method): array
    {
        $properties = $method->getParameters();
        if (\count($properties) === 0) {
            return [null, ['type' => 'object']];
        }

        if (\count($properties) > 1) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Handler method %s should have exactly one parameter or no parameters at all.',
                    $method->getName(),
                ),
            );
        }

        $schema = $properties[0];
        if ($schema->getType() === null) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Handler method %s should have exactly one parameter.',
                    $method->getName(),
                ),
            );
        }

        $schemaClass = $schema->getType()->getName();
        if (!\class_exists($schemaClass)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Handler method %s parameter should be a class, %s given.',
                    $method->getName(),
                    $schemaClass,
                ),
            );
        }

        return [
            $schemaClass,
            $this->schemaMapper->toJsonSchema($schemaClass),
        ];
    }
}
