<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\Container;
use Spiral\Config\ConfiguratorInterface;
use VendorName\Skeleton\Commands;
use VendorName\Skeleton\Config\SkeletonConfig;
use Spiral\Console\Bootloader\ConsoleBootloader;

final class SkeletonBootloader extends Bootloader
{
    protected const BINDINGS = [];
    protected const SINGLETONS = [];
    protected const DEPENDENCIES = [];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(ConsoleBootloader $console): void
    {
        $this->initConfig();

        $console->addCommand(Commands\SkeletonCommand::class);
    }

    public function boot(Container $container): void
    {
    }

    private function initConfig(): void
    {
        $this->config->setDefaults(
            SkeletonConfig::CONFIG,
            []
        );
    }
}
