<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
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
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $date_g;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDownload;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description_g;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $note;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbNote;

    /**
     * @ORM\Column(type="integer")
     */
    private $member;

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

    public function getDateG(): ?\DateTimeInterface
    {
        return $this->date_g;
    }

    public function setDateG(\DateTimeInterface $date_g): self
    {
        $this->date_g = $date_g;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getNbDownload(): ?int
    {
        return $this->nbDownload;
    }

    public function setNbDownload(int $nbDownload): self
    {
        $this->nbDownload = $nbDownload;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getDescriptionG(): ?string
    {
        return $this->description_g;
    }

    public function setDescriptionG(string $description_g): self
    {
        $this->description_g = $description_g;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getNbNote(): ?int
    {
        return $this->nbNote;
    }

    public function setNbNote(int $nbNote): self
    {
        $this->nbNote = $nbNote;

        return $this;
    }

    public function getMember(): ?int
    {
        return $this->member;
    }

    public function setMember(int $member): self
    {
        $this->member = $member;

        return $this;
    }
}
