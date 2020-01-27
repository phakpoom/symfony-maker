<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseOne;

class Dummy implements DummyInterface
{
    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $displayName;

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
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
