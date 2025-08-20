<?php

declare(strict_types=1);

namespace Spiral\McpServer\Bootloader;

use CuyZ\Valinor\Cache\FileSystemCache;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\Environment\AppEnvironment;
use Spiral\JsonSchemaGenerator\Generator as JsonSchemaGenerator;
use Spiral\McpServer\SchemaMapperInterface;
use Spiral\McpServer\Valinor\MapperBuilder;
use Spiral\McpServer\Valinor\SchemaMapper;

final class ValinorMapperBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            SchemaMapperInterface::class => static function (
                AppEnvironment $env,
                DirectoriesInterface $dirs,
                JsonSchemaGenerator $generator,
            ): SchemaMapper {
                $mapper = new MapperBuilder(
                    cache: match ($env) {
                        AppEnvironment::Production => new FileSystemCache(
                            cacheDir: $dirs->get('runtime') . 'cache/valinor',
                        ),
                        default => null,
                    },
                );

                $treeMapper = $mapper->build();

                return new SchemaMapper($generator, $treeMapper);
            },
        ];
    }
}
