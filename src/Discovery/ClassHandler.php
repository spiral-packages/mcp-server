<?php

declare(strict_types=1);

namespace Spiral\McpServer\Discovery;

use Mcp\Server\Context;
use Mcp\Server\Contracts\HandlerInterface;
use Spiral\Core\FactoryInterface;
use Spiral\McpServer\SchemaMapperInterface;

final readonly class ClassHandler implements HandlerInterface
{
    public function __construct(
        private FactoryInterface $factory,
        private SchemaMapperInterface $schemaMapper,
        private \ReflectionClass $class,
        private ?string $schemaClass = null,
    ) {}

    public function handle(
        array $arguments,
        Context $context,
    ): mixed {
        /** @var callable $tool */
        $tool = $this->factory->make($this->class->getName());

        if ($this->schemaClass === null) {
            return $tool();
        }

        $object = $this->schemaMapper->toObject(
            json: \json_encode($arguments),
            class: $this->schemaClass,
        );

        return $tool($object);
    }
}
