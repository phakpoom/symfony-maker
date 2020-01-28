<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Dummy implements DummyInterface
{
    use TimestampableTrait;

    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $displayName;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
