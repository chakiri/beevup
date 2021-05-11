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

    public function __construct()
    {
        $this->timeSlots = new ArrayCollection();
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
}