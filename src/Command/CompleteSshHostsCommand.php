<?php

namespace App\Command;

use App\Entity\Host;
use App\Service\PortMap;
use App\Service\SSHConfigFileParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'complete:sshhosts',
    description: 'list configured SSH hosts',
)]
class CompleteSshHostsCommand extends CompleteCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $hosts = SSHConfigFileParser::parse(getenv('HOME') . '/.ssh/config');
        $detail = $input->getOption('detail');
        $words = $input->getArgument('words');

        $hosts = $this->filterHosts($hosts, $words);
        if ($detail) {
            usort($hosts, function (Host $a, Host $b) {
                return strcmp($a->getName(), $b->getName());
            });
            $header = $this->makeHeader($hosts);
            $io->table($header, $this->makeTable($header, $hosts));
        } else {
            foreach ($hosts as $host) {

                $io->writeln($host->getName());

            }
        }

        return Command::SUCCESS;
    }

    private function filterHosts(array $hosts, ?string $words): array
    {
        $result = [];
        if (empty($words)) {
            return $hosts;
        }
        /**
         * @var Host $host
         */
        foreach ($hosts as $host) {
            if ($host->match($words)) {
                $result[] = $host;
            }
        }
        return $result;
    }

    private function makeHeader(array $hosts)
    {
        return $hosts[0]->fields();
    }

    private function makeTable(array $header, array $hosts)
    {
        $result = [];
        /** @var Host $host */
        foreach ($hosts as $host) {
            $hostRow = $host->toTableRow($header);
            $hostRow['localforward'] =
                $this->decorateForward($hostRow['localforward']);
            $result[] = $hostRow;
        }
        return $result;
    }

    private function decorateForward(string $substr)
    {
        if (empty($substr)) {
            return $substr;
        }
        list($src, $target) = explode(' ', $substr);
        list(, $targetPort) = explode(':', $target);
        return PortMap::getServiceForPort((int)$targetPort) . ' => ' . $src;
    }
}
