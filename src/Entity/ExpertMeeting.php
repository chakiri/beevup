<?php

namespace App\Entity;

use App\Repository\ExpertMeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpertMeetingRepository::class)
 */
class ExpertMeeting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $expertise;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVisio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isInCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=TimeSlot::class, mappedBy="expertMeeting", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $timeSlots;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="expertMeetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=ExpertBooking::class, mappedBy="expertMeeting", orphanRemoval=true)
     */
    private $expertBookings;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->timeSlots = new ArrayCollection();
        $this->expertBookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpertise(): ?string
    {
        return $this->expertise;
    }

    public function setExpertise(string $expertise): self
    {
        $this->expertise = $expertise;

        return $this;
    }

    public function getIsVisio(): ?bool
    {
        return $this->isVisio;
    }

    public function setIsVisio(bool $isVisio): self
    {
        $this->isVisio = $isVisio;

        return $this;
    }

    public function getIsInCompany(): ?bool
    {
        return $this->isInCompany;
    }

    public function setIsInCompany(bool $isInCompany): self
    {
        $this->isInCompany = $isInCompany;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|TimeSlot[]
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function addTimeSlot(TimeSlot $timeSlot): self
    {
        if (!$this->timeSlots->contains($timeSlot)) {
            $this->timeSlots[] = $timeSlot;
            $timeSlot->setExpertMeeting($this);
        }

        return $this;
    }

    public function removeTimeSlot(TimeSlot $timeSlot): self
    {
        if ($this->timeSlots->removeElement($timeSlot)) {
            // set the owning side to null (unless already changed)
            if ($timeSlot->getExpertMeeting() === $this) {
                $timeSlot->setExpertMeeting(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|ExpertBooking[]
     */
    public function getExpertBookings(): Collection
    {
        return $this->expertBookings;
    }

    public function addExpertBooking(ExpertBooking $expertBooking): self
    {
        if (!$this->expertBookings->contains($expertBooking)) {
            $this->expertBookings[] = $expertBooking;
            $expertBooking->setExpertMeeting($this);
        }

        return $this;
    }

    public function removeExpertBooking(ExpertBooking $expertBooking): self
    {
        if ($this->expertBookings->removeElement($expertBooking)) {
            // set the owning side to null (unless already changed)
            if ($expertBooking->getExpertMeeting() === $this) {
                $expertBooking->setExpertMeeting(null);
            }
        }

        return $this;
    }
}
