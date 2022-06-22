<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    protected ?int $id;
    protected float $price = 12.05;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
