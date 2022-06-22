<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    protected ?int $id;
    protected bool $active = true;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
