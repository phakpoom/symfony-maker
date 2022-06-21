<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mock implements MockInterface
{
    protected ?int $id;

    /** @var Collection<int, CommentInterface> */
    protected Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function hasComment(CommentInterface $comment): bool
    {
        return $this->comments->contains($comment);
    }

    public function addComment(CommentInterface $comment): void
    {
        if (!$this->hasComment($comment)) {
            $this->comments->add($comment);
            //$comment->setXXX($this);
        }
    }

    public function removeComment(CommentInterface $comment): void
    {
        if ($this->hasComment($comment)) {
            $this->comments->removeElement($comment);
            //$comment->setXXX(null);
        }
    }
}
