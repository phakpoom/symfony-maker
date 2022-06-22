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

    /** @var Collection<int, PostInterface> */
    protected Collection $posts;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
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

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function hasPost(PostInterface $post): bool
    {
        return $this->posts->contains($post);
    }

    public function addPost(PostInterface $post): void
    {
        if (!$this->hasPost($post)) {
            $this->posts->add($post);
            //$post->setXXX($this);
        }
    }

    public function removePost(PostInterface $post): void
    {
        if ($this->hasPost($post)) {
            $this->posts->removeElement($post);
            //$post->setXXX(null);
        }
    }
}
