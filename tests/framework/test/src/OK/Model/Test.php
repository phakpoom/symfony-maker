<?php

declare(strict_types=1);

namespace App\OK\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nette\PhpGenerator\PhpNamespace;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Test implements TestInterface
{
    use TimestampableTrait;
    use ToggleableTrait;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var Collection|PhpNamespace[]
     */
    protected $test;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->tests = new ArrayCollection();
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getTest(): Collection
    {
        return $this->test;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTest(PhpNamespace $test): bool
    {
        return $this->tests->contains($test);
    }

    /**
     * {@inheritdoc}
     */
    public function addTest(PhpNamespace $test)
    {
        if (!$this->hasTest($test)) {
            $this->tests->add($test);
            //$test->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTest(PhpNamespace $test)
    {
        if ($this->hasTest($test)) {
            $this->tests->removeElement($test);
            //$test->setXXX(null);
        }
    }
}
