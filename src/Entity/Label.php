<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LabelRepository::class)
 * @Vich\Uploadable
 */
class Label implements \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $charter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kbis;

    /**
     * @Vich\UploadableField(mapping="kbis_file", fileNameProperty="kbis")
     * @var File
     * @Assert\File(
     *     maxSize = "2048k",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Merci d'uploader un fichier valid : png, jpeg, pdf"
     * )
     */
    private $kbisFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $kbisStatus;

    /**
     * @ORM\OneToOne(targetEntity=Company::class, inversedBy="label", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLabeled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $storeAppointment;

    public function __construct()
    {
        $this->email = true;
        $this->charter = false;
        $this->isLabeled = false;
        $this->createdAt = new \Datetime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?bool
    {
        return $this->email;
    }

    public function setEmail(?bool $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCharter(): ?bool
    {
        return $this->charter;
    }

    public function setCharter(bool $charter): self
    {
        $this->charter = $charter;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): self
    {
        $this->kbis = $kbis;

        return $this;
    }

    public function setKbisFile(File $kbisFile = null)
    {
        $this->kbisFile = $kbisFile;

        if ($kbisFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getKbisFile()
    {
        return $this->kbisFile;
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

    public function serialize()
    {
        $this->kbisFile = base64_encode($this->kbisFile);
    }

    public function unserialize($serialized)
    {
        $this->kbisFile = base64_decode($this->kbisFile);

    }

    public function getKbisStatus(): ?string
    {
        return $this->kbisStatus;
    }

    public function setKbisStatus(?string $kbisStatus): self
    {
        $this->kbisStatus = $kbisStatus;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

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

    public function getSiret()
    {
        return $this->company->getSiret();
    }

    public function isLabeled(): ?bool
    {
        return $this->isLabeled;
    }

    public function setIsLabeled(bool $isLabeled): self
    {
        $this->isLabeled = $isLabeled;

        return $this;
    }

    public function isKbisValid(): bool
    {
        return $this->kbisStatus === 'isValid';
    }

    public function getStoreAppointment(): ?\DateTimeInterface
    {
        return $this->storeAppointment;
    }

    public function setStoreAppointment(?\DateTimeInterface $storeAppointment): self
    {
        $this->storeAppointment = $storeAppointment;

        return $this;
    }
}
