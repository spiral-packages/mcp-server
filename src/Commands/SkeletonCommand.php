<?php

declare(strict_types=1);

namespace VendorName\Skeleton\Commands;

use Spiral\Console\Attribute\Argument;
use Spiral\Console\Attribute\Option;
use Spiral\Console\Attribute\Question;
use Spiral\Console\Command;
use Spiral\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'skeleton', description: 'My command')]
final class SkeletonCommand extends Command
{
    protected const SIGNATURE = 'skeleton {argument : Argument description} {--o|option : Option description}';

    #[Argument(description: 'Argument description')]
    #[Question(question: 'Provide argument')]
    private string $argument;

    #[Option(shortcut: 'o', description: 'Option description')]
    private bool $option;


    public function __invoke(): int
    {
        return self::SUCCESS;
    }
}
