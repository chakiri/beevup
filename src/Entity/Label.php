<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=LabelRepository::class)
 * @Vich\Uploadable
 */
class Label
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="label", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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
     */
    private $kbisFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
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
}
