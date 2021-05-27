<?php

namespace App\Entity;

use App\Repository\ExpertBookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExpertBookingRepository::class)
 */
class ExpertBooking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ExpertMeeting::class, inversedBy="expertBookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expertMeeting;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="expertBookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $way;

    /**
     * @ORM\OneToOne(targetEntity=Slot::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Vous devez choisir un créneau")
     */
    private $slot;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpertMeeting(): ?ExpertMeeting
    {
        return $this->expertMeeting;
    }

    public function setExpertMeeting(?ExpertMeeting $expertMeeting): self
    {
        $this->expertMeeting = $expertMeeting;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWay()
    {
        return $this->way;
    }

    /**
     * @param mixed $way
     */
    public function setWay($way): void
    {
        $this->way = $way;
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

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(Slot $slot): self
    {
        $this->slot = $slot;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
