<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'complete:project',
    description: 'outputs list of paramaters for project completion',
)]
class CompleteProjectCommand extends CompleteCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->addOption(
            'parent',
            'p',
            InputOption::VALUE_REQUIRED,
            'parent folder'
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $detail = $input->getOption('detail');
        $words = $input->getArgument('words');
        $path = $input->getOption('parent') . '/*';
        if ($path === '/*') {
            $output->writeln('<error>missing path option</error>');
            return Command::FAILURE;
        }
        $project_folder_list = glob($path, GLOB_ONLYDIR);
        $project_folder_list = array_filter($project_folder_list, function ($path) {
            return file_exists($path . '/.git');
        });
        $project_folder_list = array_map(function ($result) {
            return basename($result);
        }, $project_folder_list);
        if ($words !== null) {
            $project_folder_list = array_filter($project_folder_list, function ($path) use ($words) {
                return str_starts_with(strtolower($path), strtolower($words)) && (strtolower($path) !== strtolower($words));
            });
        }
        $project_folder_list = array_map(function ($result) {
            if (str_contains($result, ' ')) {
                $result = '"' . $result . '"';
            }
            $result .= ' ';
            return $result;
        }, $project_folder_list);
        if ($detail) {
            $output->writeln(sprintf('<info>Number of Folders: %s</info>', count($project_folder_list)));
        } else {
            $output->writeln($project_folder_list);
        }
        return Command::SUCCESS;
    }

}
