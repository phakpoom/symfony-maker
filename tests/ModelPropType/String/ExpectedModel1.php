<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    protected ?int $id;
    protected string $name = 'bon';

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
