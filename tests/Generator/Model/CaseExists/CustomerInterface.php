<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

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
}
