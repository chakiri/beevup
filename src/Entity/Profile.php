<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfilRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Profile implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex("/\d{10}|\+33\d{9}|\+33\s\d{1}\s\d{2}\s\d{2}\s\d{2}\s\d{2}|\d{2}\s\d{2}\s\d{2}\s\d{2}\s\d{2}/")
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex("/\d{10}|\+33\d{9}|\+33\s\d{1}\s\d{2}\s\d{2}\s\d{2}\s\d{2}|\d{2}\s\d{2}\s\d{2}\s\d{2}\s\d{2}/")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="profile_image", fileNameProperty = "filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=1500, nullable=true)
     */
    private $introduction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFunction")
     */
    private $function;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isOnboarding;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $jobTitle;

    private $utility;

    public function __construct()
    {
        $this->isCompleted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction =str_replace("\r\n",'<br>', $introduction);

        return $this;
    }

    public function getFunction(): ?UserFunction
    {
        return $this->function;
    }

    public function setFunction(?UserFunction $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function getIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }
    public function __toString()
    {
       //return strval( $this->getId() );
        return strval( $this->getFirstname().' '.$this->getLastname() );
      
    }

    public function serialize()
    {
        return serialize($this->id);
    }

    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);

    }

    public function getIsOnboarding(): ?bool
    {
        return $this->isOnboarding;
    }

    public function setIsOnboarding(?bool $isOnboarding): self
    {
        $this->isOnboarding = $isOnboarding;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updatesNames()
    {
        $this->setFirstname($this->updateName($this->getFirstname()));
        $this->setLastname($this->updateName($this->getLastname()));
    }

    private function updateName(string $name)
    {
        $updateName = null;
        $pieces = explode("-", $name);
        foreach ($pieces as $piece){
            $val = ucfirst(strtolower($piece));
            $updateName =  (!$updateName) ? $val : $updateName . '-' . $val;
        }
        return $updateName;
    }

}
