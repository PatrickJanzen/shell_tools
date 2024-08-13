<?php

declare(strict_types=1);

namespace App\Command;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class CompleteCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption(
            'detail',
            'd',
            InputOption::VALUE_NONE,
            'shows detailed information'
        );
        $this->addArgument(
            'words',
            InputArgument::OPTIONAL,
            '"${COMP_WORDS[1]}"'
        );
    }


}