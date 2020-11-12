<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @Vich\Uploadable
 */
class Post implements \Serializable
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
     * @ORM\Column(type="string", length=1500, nullable=true)
     */
    private $description;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post" , cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostLike", mappedBy="post" , cascade={"persist", "remove"})
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Abuse", mappedBy="post" , cascade={"persist", "remove"})
     */
    private $abuses;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distinctUserCommentNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company", cascade={"persist", "remove"})
     */
    private $toCompany;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $urlYoutube;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_image", fileNameProperty = "filename")
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity=PostCategory::class)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $urlLink;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $imageLink;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $relatedTo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $relatedToType;

    /**
     * @ORM\OneToOne(targetEntity=Recommandation::class, cascade={"persist", "remove"})
     */
    private $relatedToRecommandation;

     public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->abuses = new ArrayCollection();

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

        $this->description =str_replace("\r\n",'<br />', $description);

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

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
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

    /**
     * @return Collection
     */
    public function getAbuses(): Collection
    {
        return $this->abuses;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getToCompany(): ?Company
    {
        return $this->toCompany;
    }

    public function setToCompany(?Company $toCompany): self
    {
        $this->toCompany = $toCompany;

        return $this;
    }

    public function getUrlYoutube(): ?string
    {
        return $this->urlYoutube;
    }

    public function setUrlYoutube(?string $urlYoutube): self
    {
        $this->urlYoutube = $urlYoutube;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param null|string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     * @return $this
     */
    public function setImageFile(?File $imageFile)
    {
        $this->imageFile = $imageFile;

        if ($this->imageFile instanceof UploadedFile) {
            $this->modifiedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
 * Check if post liked by useré
 * @param User $user
 * @return bool
 */
    public function isLikedByUser(User $user): bool{
        foreach ($this->likes as $like){
            if ($like->getUser() == $user) return true;
        }
        return false;
    }

    /**
     * Check if post is reported by current user
     * @param User $user
     * @return bool
     */
    public function isReportedByUser(User $user): bool{
        foreach ($this->abuses as $abus){
            if ($abus->getUser() == $user) return true;
        }
        return false;
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);

    }

    public function getCategory(): ?PostCategory
    {
        return $this->category;
    }

    public function setCategory(?PostCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrlLink()
    {
        return $this->urlLink;
    }

    /**
     * @param mixed $urlLink
     */
    public function setUrlLink($urlLink)
    {
        $this->urlLink = $urlLink;
    }

    /**
     * @return mixed
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param mixed $imageLink
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    public function getRelatedTo(): ?int
    {
        return $this->relatedTo;
    }

    public function setRelatedTo(?int $relatedTo): self
    {
        $this->relatedTo = $relatedTo;

        return $this;
    }

    public function getRelatedToType(): ?string
    {
        return $this->relatedToType;
    }

    public function setRelatedToType(?string $relatedToType): self
    {
        $this->relatedToType = $relatedToType;

        return $this;
    }

    public function getRelatedToRecommandation(): ?Recommandation
    {
        return $this->relatedToRecommandation;
    }

    public function setRelatedToRecommandation(?Recommandation $relatedToRecommandation): self
    {
        $this->relatedToRecommandation = $relatedToRecommandation;

        return $this;
    }




}
