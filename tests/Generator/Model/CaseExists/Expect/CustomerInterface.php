<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use App\App\Model\DummyInterface;

interface CustomerInterface extends ResourceInterface
{
    /**
     * @return Collection|DummyInterface[]
     */
    public function getGroups(): Collection;

    /**
     * @param DummyInterface $group
     *
     * @return bool
     */
    public function hasGroup(DummyInterface $group): bool;

    /**
     * @param DummyInterface $group
     *
     * @return void
     */
    public function addGroup(DummyInterface $group): void;

    /**
     * @param DummyInterface $group
     *
     * @return void
     */
    public function removeGroup(DummyInterface $group): void;

    /**
     * @return string|null
     */
    public function getMainName(): ?string;

    /**
     * @param string|null $mainName
     *
     * @return void
     */
    public function setMainName(?string $mainName): void;

    /**
     * @return Collection|DummyInterface[]
     */
    public function getNames(): Collection;

    /**
     * @param DummyInterface $name
     *
     * @return bool
     */
    public function hasName(DummyInterface $name): bool;

    /**
     * @param DummyInterface $name
     *
     * @return void
     */
    public function addName(DummyInterface $name): void;

    /**
     * @param DummyInterface $name
     *
     * @return void
     */
    public function removeName(DummyInterface $name): void;
}
