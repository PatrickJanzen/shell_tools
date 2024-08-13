<?php

declare(strict_types=1);

namespace App\Entity;


class Host
{

    private string $name;
    private $options = [];

    public function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->addOption('Alias', $name);
    }

    public function addOption(string $name, string $value = '', bool $force = false): void
    {
        $name = strtolower((string)$name);
        if (!array_key_exists($name, $this->options) || (empty($this->options) && !empty($value)) || $force) {
            $this->options[$name] = $value;
        }
    }

    public function fillOptions(array $options): void
    {
        foreach ($options as $name => $value) {
            $this->addOption($name);
        }
    }

    public function fillBase(Host $host): void
    {
        foreach ($host->options as $name => $value) {
            $this->addOption($name, $value);
        }
    }

    public function match(string $words)
    {
        return str_starts_with($this->name, $words) && $this->name !== $words;
    }

    public function fields(): array
    {
        return array_keys($this->options);
    }

    public function toTableRow(array $header)
    {
        $result = array_fill_keys($header, '');
        foreach ($this->options as $name => $value) {
            $result[$name] = $value;
        }
        return $result;
    }

}