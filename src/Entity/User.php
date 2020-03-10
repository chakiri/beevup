<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * * @UniqueEntity(fields="email", message="Adresse e-mail déjà prise")
 */
class User implements  UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=8)
     * @Assert\Length(max=4096)
     * @Assert\Regex(
     *     pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$^",
     *     match=true,
     *     message="Votre mot de passe doit contenir 1 Majuscule et 1 minuscule"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    
    /**
     * @ORM\Column(type="integer")
    */

    private $type;


    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="array")
     */
    private $storeIds = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $companyId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $optin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidEmail;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

    public function __construct()
  {
    /*$this->emailValide = 0;
    $this->status = 0;
    $this->dateDeCreation = new \Datetime();
    $this->dateDeModification = new \Datetime();
    $this->roles = ['User'];*/

  }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getStoreIds(): ?array
    {
        return $this->storeIds;
    }

    public function setStoreIds(array $storeIds): self
    {
        $this->storeIds = $storeIds;

        return $this;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getOptin(): ?bool
    {
        return $this->optin;
    }

    public function setOptin(bool $optin): self
    {
        $this->optin = $optin;

        return $this;
    }

    public function getIsValidEmail(): ?bool
    {
        return $this->isValidEmail;
    }

    public function setIsValidEmail(bool $isValidEmail): self
    {
        $this->isValidEmail = $isValidEmail;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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
    
    public function getUsername()
    {
        return $this->email;
    }
    
    public function getSalt()
    {
        return '';
    }
    public function eraseCredentials()
    {
       
    }
    
}
