<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface DummyInterface extends ResourceInterface, TimestampableInterface
{
    public function getDisplayName(): ?string;

    public function setDisplayName(?string $displayName): void;

    public function getAge(): ?int;

    public function setAge(?int $age): void;

    /**
     * @return Collection<int, ResourceInterface>
     */
    public function getGroups(): Collection;

    public function hasGroup(ResourceInterface $group): bool;

    public function addGroup(ResourceInterface $group): void;

    public function removeGroup(ResourceInterface $group): void;
}
