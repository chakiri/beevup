<?php

namespace App\Entity;

use App\Service\Map\GeocodeAddress;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * @UniqueEntity("siret", message="Siret déjà prise")
 */
class Company implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Regex(pattern="/^[0-9]{14}+$/", message="Siret invalide")
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $siret;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex("/\d{10}|\+33\d{9}|\+33\s\d{1}\s\d{2}\s\d{2}\s\d{2}\s\d{2}|\d{2}\s\d{2}\s\d{2}\s\d{2}\s\d{2}/")
     */
    private $phone;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressNumber;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", nullable=true)
     */
    private $addressStreet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressPostCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="company_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     * pattern="(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})",
     * match=true,
     * message="Veuillez saisir une URL valide"
     * )
     */
    private $video;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1500, nullable=true)
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Assert\Regex(
     * pattern="(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})",
     * match=true,
     * message="Veuillez saisir une URL valide"
     * )
     */
    private $website;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="companies", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanyCategory")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service")
     */
    private $services;

    /**
     * @ORM\Column(type="text")
     */
    private $barCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $otherCategory;

    /**
     * @ORM\OneToOne(targetEntity=Subscription::class, mappedBy="company", cascade={"persist", "remove"})
     */
    private $subscription;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->isValid = false;
        $this->isCompleted = false;
        $this->updatedAt = new \Datetime();
        $this->services = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

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

    public function setAddressNumber(?string $addressNumber): self
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getAddressStreet(): ?string
    {
        return $this->addressStreet;
    }

    public function setAddressStreet(?string $addressStreet): self
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    public function getAddressPostCode(): ?string
    {
        return $this->addressPostCode;
    }

    public function setAddressPostCode(?string $addressPostCode): self
    {
        $this->addressPostCode = $addressPostCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

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

    public function getDescription():  ?string
    {
        return $this->description;

    }

    public function setDescription(?string $description): self
    {
        $this->description =str_replace("\r\n",'<br>', $description);


        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

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

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

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
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getCategory(): ?CompanyCategory
    {
        return $this->category;
    }

    public function setCategory(?CompanyCategory $category): self
    {
        $this->category = $category;

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

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
       return strval( $this->getName() );
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * @return Collection|Sponsorship[]
     */
    public function getSponsorships(): Collection
    {
        return $this->sponsorship;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        return $this;
    }

    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    public function setBarCode(string $barCode): self
    {
        $this->barCode = $barCode;

        return $this;
    }

    public function getOtherCategory(): ?string
    {
        return $this->otherCategory;
    }

    public function setOtherCategory(?string $otherCategory): self
    {
        $this->otherCategory = $otherCategory;

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

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

        // set the owning side of the relation if necessary
        if ($subscription->getCompany() !== $this) {
            $subscription->setCompany($this);
        }

        return $this;
    }

    /**
     * get company administrator
     */
    public function getCompanyAdministrator() {
        foreach ($this->users as $user){
           if ( $user->getType()->getId() === 3) return $user;
        }
    }

    public function getCompanyAdministratorFullName() {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                if($user->getProfile()->getFirstName() != '')
                     return $user->getProfile()->getFirstName().' '.$user->getProfile()->getLastname();
                else return 'N/C';
        }
    }

    public function getEmailAdministrator() {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                return $user->getEmail();
        }
    }

    public function getServiceNumber(){
        return count($this->services);
    }

    public function isProfileAdminCompleted(){
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3)
                return ( $user->getProfile()->getIsCompleted()) ? 'Oui' : 'Non';
        }
    }

    public function isLogoAdminCompleted(){
        $isLogoAdminDefined = null;
        foreach ($this->users as $user){
            if ( $user->getType()->getId() === 3) {
                $isLogoAdminDefined = ($user->getProfile()->getFileName() != '') ? 'Oui' : 'Non';
                return $isLogoAdminDefined;
            }
        }
    }

    public function isLogoDefined(){
        return $isLogoDefined = ($this->getFilename() !='') ? 'Oui' : 'Non';
    }

    public function getCreatedAt(){
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3) {
                if($user->getCreatedAt()) {
                    return $user->getCreatedAt();
                }
            }
        }
    }

    public function getCreatedAtFormat(){
        return $this->getCreatedAt()->format('d/m/Y');
    }

    public function getScore()
    {
        foreach ($this->users as $user){
            if ( $user->getType()->getId() == 3) {
                if($user->getScore() != null && $user->getScore() != '')
                    return $user->getScore()->getPoints();
                else return '0';
            }
        }
    }

    public function getSponsorshipNumber(){
        $users = $this->getUsers();
        $nb = 0;
        foreach ($users as $user){
            $nb +=count($user->getSponsorship());
        }
        return $nb;
    }

}
