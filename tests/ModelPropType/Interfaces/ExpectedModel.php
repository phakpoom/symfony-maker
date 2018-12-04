<?php

declare(strict_types=1);

namespace App\Model;

use MyApp\Model\CategoryInterface;

class Mock implements MockInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var CategoryInterface|null
     */
    protected $category;

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
    public function getCategory(): ?CategoryInterface
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(?CategoryInterface $category): void
    {
        $this->category = $category;
    }
}
