<?php

declare(strict_types=1);

namespace App\Model;

use MyApp\Model\CategoryInterface;

class Mock implements MockInterface
{
    protected ?int $id;
    protected ?CategoryInterface $category;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?CategoryInterface
    {
        return $this->category;
    }

    public function setCategory(?CategoryInterface $category): void
    {
        $this->category = $category;
    }
}
