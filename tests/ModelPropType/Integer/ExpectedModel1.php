<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    protected ?int $id;
    protected int $price = 12;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }
}
