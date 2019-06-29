<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    /** @var int|null */
    protected $id;

    /** @var float */
    protected $price = 12.05;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
