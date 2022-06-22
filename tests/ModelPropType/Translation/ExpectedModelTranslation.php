<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

class MockTranslation extends AbstractTranslation implements MockTranslationInterface
{
    protected ?int $id;
    protected ?string $name;
    protected ?string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
