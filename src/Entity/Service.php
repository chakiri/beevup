<?php

namespace App\Entity;

use App\Entity\Traits\SeveralFiles;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 * @Vich\Uploadable
 */
class Service implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Length(max=1500)
     * @ORM\Column(type="string", length=1500)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeService")
     */
    private $type;
    
    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $introduction;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $filename;
    
    /**
     * @var File|null
     * @Vich\UploadableField(mapping="service_image", fileNameProperty = "filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    use SeveralFiles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ServiceCategory")
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isQuote;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDiscovery;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discoveryContent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $toIndividuals;

    /**
     * @ORM\Column(type="boolean")
     */
    private $toProfessionals;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $vatRate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unity;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
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
        $this->description =str_replace("\r\n",'<br>', $description);

        return $this;
    }

    public function getType(): ?TypeService
    {
        return $this->type;
    }

    public function setType(TypeService $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }
    
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }

    public function setCategory(?ServiceCategory $category): self
    {
        $this->category = $category;

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
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getIsQuote(): ?bool
    {
        return $this->isQuote;
    }

    public function setIsQuote(bool $isQuote): self
    {
        $this->isQuote = $isQuote;

        return $this;
    }

    public function getIsDiscovery(): ?bool
    {
        return $this->isDiscovery;
    }

    public function setIsDiscovery(?bool $isDiscovery): self
    {
        $this->isDiscovery = $isDiscovery;

        return $this;
    }

    public function getDiscoveryContent(): ?string
    {
        return $this->discoveryContent;
    }

    public function setDiscoveryContent(?string $discoveryContent): self
    {
        $this->discoveryContent = $discoveryContent;

        return $this;
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);

    }

    public function getToIndividuals(): ?bool
    {
        return $this->toIndividuals;
    }

    public function setToIndividuals(bool $toIndividuals): self
    {
        $this->toIndividuals = $toIndividuals;

        return $this;
    }

    public function getToProfessionals(): ?bool
    {
        return $this->toProfessionals;
    }

    public function setToProfessionals(bool $toProfessionals): self
    {
        $this->toProfessionals = $toProfessionals;

        return $this;
    }

    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }

    public function setVatRate(?string $vatRate): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(?string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }
}
