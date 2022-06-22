<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use App\App\Model\DummyInterface;

interface CustomerInterface extends ResourceInterface
{
    /**
     * @return Collection<int, DummyInterface>
     */
    public function getGroups(): Collection;

    public function hasGroup(DummyInterface $group): bool;

    public function addGroup(DummyInterface $group): void;

    public function removeGroup(DummyInterface $group): void;

    public function getMainName(): ?string;

    public function setMainName(?string $mainName): void;

    /**
     * @return Collection<int, DummyInterface>
     */
    public function getNames(): Collection;


    public function hasName(DummyInterface $name): bool;

    public function addName(DummyInterface $name): void;

    public function removeName(DummyInterface $name): void;
}
