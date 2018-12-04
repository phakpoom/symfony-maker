<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $name = 'bon';

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
