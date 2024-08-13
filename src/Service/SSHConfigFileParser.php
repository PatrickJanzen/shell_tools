<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Host;

class SSHConfigFileParser
{

    public static function parse(string $file)
    {

        $in = self::loadFile($file);
        $hosts = [];
        $baseHosts = [];
        $host = null;
        $options = ['Alias' => true];
        foreach (explode("\n", $in) as $line) {
            if (str_starts_with($line, 'Host')) {
                if ($host !== null) {
                    if (str_contains($host->getName(), '*')) {
                        $baseHosts[$host->getName()] = $host;
                    } else {
                        $hosts[$host->getName()] = $host;
                    }
                }
                $name = trim(substr($line, strpos($line, ' ')));
                $host = new Host();
                $host->setName($name);
                continue;
            }
            if (strlen(trim($line)) > 0) {
                $parts = explode(' ', trim($line));
                $key = array_shift($parts);

                $host->addOption($key, implode(' ', $parts));
                $options[$key] = true;
            }

        }

        if ($host !== null) {
            if (str_contains($host->getName(), '*')) {
                $baseHosts[$host->getName()] = $host;
            } else {
                $hosts[$host->getName()] = $host;
            }
        }

        foreach ($baseHosts as $nameB => $baseHost) {
            foreach ($hosts as $name => $host) {
                if (fnmatch($nameB, $name)) {
                    $host->fillBase($baseHost);
                }

            }
        }
        foreach ($hosts as $name => $host) {
            $host->fillOptions($options);
        }
        return $hosts;
    }

    private static function loadFile(string $file): string
    {
        static $loadedFiles = [];
        $realPath = realpath($file);
        $result = [];
        if (!isset($loadedFiles[$realPath])) {
            $lines = explode(PHP_EOL, file_get_contents($realPath));
            foreach ($lines as $line) {
                if (str_starts_with(strtolower($line), 'include')) {
                    $filePath = explode('include ', $line)[1];
                    $pathParts = explode('/', $file);
                    array_pop($pathParts);
                    $basePath = implode('/', $pathParts);
                    $result[] = self::loadFile($basePath . '/' . $filePath);
                } elseif (strlen(trim($line)) > 0) {
                    $result[] = $line;
                }
            }
        }
        return implode(PHP_EOL, $result);
    }

}