<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\App\Model\DummyInterface;

class Customer implements CustomerInterface
{
    protected ?int $id;

    /** @var Collection<int, DummyInterface> */
    protected Collection $groups;
    protected ?string $mainName;

    /** @var Collection<int, DummyInterface> */
    protected Collection $names;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->abc = new ArrayCollection();
        $this->names = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function hasGroup(DummyInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    public function addGroup(DummyInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    public function removeGroup(DummyInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }

    public function getMainName(): ?string
    {
        return $this->mainName;
    }

    public function setMainName(?string $mainName): void
    {
        $this->mainName = $mainName;
    }

    public function getNames(): Collection
    {
        return $this->names;
    }

    public function hasName(DummyInterface $name): bool
    {
        return $this->names->contains($name);
    }

    public function addName(DummyInterface $name): void
    {
        if (!$this->hasName($name)) {
            $this->names->add($name);
            //$name->setXXX($this);
        }
    }

    public function removeName(DummyInterface $name): void
    {
        if ($this->hasName($name)) {
            $this->names->removeElement($name);
            //$name->setXXX(null);
        }
    }
}
