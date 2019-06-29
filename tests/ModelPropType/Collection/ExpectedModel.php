<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mock implements MockInterface
{
    /** @var int|null */
    protected $id;

    /** @var Collection|CommentInterface[] */
    protected $comments;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
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
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasComment(CommentInterface $comment): bool
    {
        return $this->comments->contains($comment);
    }

    /**
     * {@inheritdoc}
     */
    public function addComment(CommentInterface $comment): void
    {
        if (!$this->hasComment($comment)) {
            $this->comments->add($comment);
            //$comment->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeComment(CommentInterface $comment): void
    {
        if ($this->hasComment($comment)) {
            $this->comments->removeElement($comment);
            //$comment->setXXX(null);
        }
    }
}
