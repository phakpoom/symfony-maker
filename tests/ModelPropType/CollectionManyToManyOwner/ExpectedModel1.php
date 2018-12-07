<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Mock implements MockInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var Collection|CommentInterface[]
     */
    protected $comments;

    /**
     * @var Collection|PostInterface[]
     */
    protected $posts;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
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

    /**
     * {@inheritdoc}
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPost(PostInterface $post): bool
    {
        return $this->posts->contains($post);
    }

    /**
     * {@inheritdoc}
     */
    public function addPost(PostInterface $post): void
    {
        if (!$this->hasPost($post)) {
            $this->posts->add($post);
            //$post->setXXX($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePost(PostInterface $post): void
    {
        if ($this->hasPost($post)) {
            $this->posts->removeElement($post);
            //$post->setXXX(null);
        }
    }
}
