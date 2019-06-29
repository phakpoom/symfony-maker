<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    /** @var int|null */
    protected $id;

    /** @var array */
    protected $configs = [];

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
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
