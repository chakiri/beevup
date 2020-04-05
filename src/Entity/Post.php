<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likesNumber;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commentsNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post" , cascade={"persist", "remove"})
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostLike", mappedBy="post" , cascade={"persist", "remove"})
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distinctUserCommentNumber;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->comment = new ArrayCollection();
        $this->likes = new ArrayCollection();
      
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLikesNumber(): ?int
    {
        return $this->likesNumber;
    }

    public function setLikesNumber(?int $likesNumber)
    {
        return $this->likesNumber = $likesNumber;
    }

    public function getCommentsNumber(): ?int
    {
        return $this->commentsNumber;
    }

    public function setCommentsNumber(?int $commentsNumber)
    {
        return $this->commentsNumber = $commentsNumber;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->contains($comment)) {
            $this->comment->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $postLike): self
    {
        if (!$this->likes->contains($postLike)) {
            $this->likes[] = $postLike;
            $postLike->setPost($this);
        }

        return $this;
    }

    public function removeLike(PostLike $postLike): self
    {
        if ($this->likes->contains($postLike)) {
            $this->likes->removeElement($postLike);
            // set the owning side to null (unless already changed)
            if ($postLike->getPost() === $this) {
                $postLike->setPost(null);
            }
        }

        return $this;
    }

    public function getDistinctUserCommentNumber(): ?int
    {
        return $this->distinctUserCommentNumber;
    }

    public function setDistinctUserCommentNumber(?int $distinctUserCommentNumber): self
    {
        $this->distinctUserCommentNumber = $distinctUserCommentNumber;

        return $this;
    }
}
