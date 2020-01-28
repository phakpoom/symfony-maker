<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\App\Model\DummyInterface;

class Customer implements CustomerInterface
{
    /** @var int|null */
    protected $id;

    /** @var Collection|DummyInterface[] */
    protected $groups;

    /** @var string|null */
    protected $mainName;

    /** @var Collection|DummyInterface[] */
    protected $names;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->names = new ArrayCollection();
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
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup(DummyInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(DummyInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(DummyInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMainName(): ?string
    {
        return $this->mainName;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainName(?string $mainName): void
    {
        $this->mainName = $mainName;
    }

    /**
     * {@inheritdoc}
     */
    public function getNames(): Collection
    {
        return $this->names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasName(DummyInterface $name): bool
    {
        return $this->names->contains($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addName(DummyInterface $name): void
    {
        if (!$this->hasName($name)) {
            $this->names->add($name);
            //$name->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeName(DummyInterface $name): void
    {
        if ($this->hasName($name)) {
            $this->names->removeElement($name);
            //$name->setXXX(null);
        }
    }
}
