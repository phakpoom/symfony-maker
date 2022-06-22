<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseOne;

class Dummy implements DummyInterface
{
    protected ?int $id;
    protected ?string $displayName;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
