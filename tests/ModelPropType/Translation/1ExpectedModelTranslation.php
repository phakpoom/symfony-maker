<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

class MockTranslation extends AbstractTranslation implements MockTranslationInterface
{
    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $name;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
