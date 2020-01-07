<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
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
    private $description_c;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_c;

    /**
     * @ORM\Column(type="integer")
     */
    private $member;

    /**
     * @ORM\Column(type="integer")
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescriptionC(): ?string
    {
        return $this->description_c;
    }

    public function setDescriptionC(string $description_c): self
    {
        $this->description_c = $description_c;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->date_c;
    }

    public function setDateC(\DateTimeInterface $date_c): self
    {
        $this->date_c = $date_c;

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

    public function getGame(): ?int
    {
        return $this->game;
    }

    public function setGame(int $game): self
    {
        $this->game = $game;

        return $this;
    }
}
