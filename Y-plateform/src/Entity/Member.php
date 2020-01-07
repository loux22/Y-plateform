<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 */
class Member
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $postal;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $city;

    /**
     * @ORM\Column(type="date")
     */
    private $date_m;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive_m;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $IPAdress;

    /**
     * @ORM\Column(type="boolean")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): self
    {
        $this->postal = $postal;

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

    public function getDateM(): ?\DateTimeInterface
    {
        return $this->date_m;
    }

    public function setDateM(\DateTimeInterface $date_m): self
    {
        $this->date_m = $date_m;

        return $this;
    }

    public function getIsActiveM(): ?bool
    {
        return $this->isActive_m;
    }

    public function setIsActiveM(bool $isActive_m): self
    {
        $this->isActive_m = $isActive_m;

        return $this;
    }

    public function getIPAdress(): ?string
    {
        return $this->IPAdress;
    }

    public function setIPAdress(string $IPAdress): self
    {
        $this->IPAdress = $IPAdress;

        return $this;
    }

    public function getLevel(): ?bool
    {
        return $this->level;
    }

    public function setLevel(bool $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;

        return $this;
    }
}
