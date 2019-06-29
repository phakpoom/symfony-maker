<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    /** @var int|null */
    protected $id;

    /** @var bool */
    protected $active = true;

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
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
