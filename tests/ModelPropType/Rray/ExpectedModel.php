<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    protected ?int $id;
    protected array $configs = [];

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfigs(): array
    {
        return (array) $this->configs;
    }

    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
