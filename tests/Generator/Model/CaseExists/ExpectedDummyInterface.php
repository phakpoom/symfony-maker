<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface DummyInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string|null
     */
    public function getDisplayName(): ?string;

    /**
     * @param string|null $displayName
     *
     * @return void
     */
    public function setDisplayName(?string $displayName): void;

    /**
     * @return int|null
     */
    public function getAge(): ?int;

    /**
     * @param int|null $age
     *
     * @return void
     */
    public function setAge(?int $age): void;

    /**
     * @return Collection|ResourceInterface[]
     */
    public function getGroups(): Collection;

    /**
     * @param ResourceInterface $group
     *
     * @return bool
     */
    public function hasGroup(ResourceInterface $group): bool;

    /**
     * @param ResourceInterface $group
     *
     * @return void
     */
    public function addGroup(ResourceInterface $group): void;

    /**
     * @param ResourceInterface $group
     *
     * @return void
     */
    public function removeGroup(ResourceInterface $group): void;
}
