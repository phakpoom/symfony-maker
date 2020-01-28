<?php

declare(strict_types=1);

namespace Test\Generator\Model\CaseExists;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

class Dummy implements DummyInterface
{
    use TimestampableTrait;

    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $displayName;

    /** @var int|null */
    protected $age;

    /** @var Collection|ResourceInterface[] */
    protected $groups;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->groups = new ArrayCollection();
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
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * {@inheritdoc}
     */
    public function setAge(?int $age): void
    {
        $this->age = $age;
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
    public function hasGroup(ResourceInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(ResourceInterface $group): void
    {
        if (!$this->hasGroup($group)) {
            $this->groups->add($group);
            //$group->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(ResourceInterface $group): void
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
            //$group->setXXX(null);
        }
    }
}
