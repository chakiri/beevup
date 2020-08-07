<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 */
class Offer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbServices;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbUsers;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(?int $km): self
    {
        $this->km = $km;

        return $this;
    }

    public function getNbServices(): ?int
    {
        return $this->nbServices;
    }

    public function setNbServices(int $nbServices): self
    {
        $this->nbServices = $nbServices;

        return $this;
    }

    public function getNbUsers(): ?int
    {
        return $this->nbUsers;
    }

    public function setNbUsers(int $nbUsers): self
    {
        $this->nbUsers = $nbUsers;

        return $this;
    }
}
