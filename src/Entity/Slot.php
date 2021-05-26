<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SlotRepository::class)
 */
class Slot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TimeSlot::class, inversedBy="slots", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeSlot;

    /**
     * @ORM\Column(type="time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
