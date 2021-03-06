<?php

namespace App\Entity;

use App\Service\Map\GeocodeAddress;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Store implements \Serializable
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
    private $reference;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string")
     */
    private $addressNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addressStreet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addressPostCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="store_image", fileNameProperty = "filename")
     */
    private $imageFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="store")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="store", cascade={"remove"})
     */
    private $companies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $introduction;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @Assert\Length(max=1500)
     * @ORM\Column(type="string", length=1500, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $defaultAdviser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StoreService", mappedBy="store", orphanRemoval=true)
     */
    private $services;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $externalCompanies;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAdmin;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->externalCompanies = [];
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initSlug()
    {
        if (empty($this->getSlug() )){
            $slugify = new Slugify();
            $slug = $slugify->slugify($this->getName());
            $this->setSlug($slug);        
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function geolocate()
    {
        $map = new GeocodeAddress();
        if (($coordonnees = $map->geocode($this))) {
            $this->setLatitude($coordonnees[0]);
            $this->setLongitude($coordonnees[1]);
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function modfiedAt()
    {
        $this->setModifiedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(string $addressNumber): self
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getAddressStreet(): ?string
    {
        return $this->addressStreet;
    }

    public function setAdresseRue(string $addressStreet): self
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    public function getAddressPostCode(): ?string
    {
        return $this->addressPostCode;
    }

    public function setAddressPostCode(string $addressPostCode): self
    {
        $this->addressPostCode = $addressPostCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

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

    public function setAddressStreet(string $addressStreet): self
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }


    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setStore($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getStore() === $this) {
                $user->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setStore($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getStore() === $this) {
                $company->setStore(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description =str_replace("\r\n",'<br>', $description);

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
    public function __toString()
    {
       return strval( $this->getName() );
    }

    public function getDefaultAdviser(): ?User
    {
        return $this->defaultAdviser;
    }

    public function setDefaultAdviser(?User $defaultAdviser): self
    {
        $this->defaultAdviser = $defaultAdviser;

        return $this;
    }

    /**
     * @return Collection|StoreService[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(StoreService $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setStore($this);
        }

        return $this;
    }

    public function removeService(StoreService $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getStore() === $this) {
                $service->setStore(null);
            }
        }

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

    public function getExternalCompanies(): ?array
    {
        return $this->externalCompanies;
    }

    public function setExternalCompanies(?array $externalCompanies): self
    {
        $this->externalCompanies = $externalCompanies;

        return $this;
    }

    public function addExternalCompany(int $idExternalCompany): self
    {
        if (!in_array($idExternalCompany, $this->externalCompanies)){
            array_push($this->externalCompanies, $idExternalCompany);
        }

        return $this;
    }

    public function removeExternalCompany(int $idExternalCompany): self
    {
        if (in_array($idExternalCompany, $this->externalCompanies)){
            //Get the key of id
            $key = array_search($idExternalCompany, $this->externalCompanies);
            //Delete the key from the array
            unset($this->externalCompanies[$key]);
        }

        return $this;
    }

    public function haveExternalCompany(int $idExternalCompany): bool
    {
        if (in_array($idExternalCompany, $this->externalCompanies)){
            return true;
        }
        return false;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
