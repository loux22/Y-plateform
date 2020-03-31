<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Error;

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
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Error\Length(min=10, max=10, minMessage="le numero de telephone '{{ value }}' n'est pas valide",
     * maxMessage="le numero de telephone '{{ value }}' n'est pas valide")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Error\Length(min=8, max=50, minMessage="ton adresse {{ value }} est trop courte", 
     * maxMessage="Ton adresse '{{ value }}' est trop longue")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Error\Length(min=5, max=5, minMessage="ton code postal {{ value }} n'est pas valide", 
     * maxMessage="Ton code postal '{{ value }}' n'est pas valide")
     */
    private $postal;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Error\Length(min=2, max=30, minMessage="le nom de la ville '{{ value }}' est trop court", 
     * maxMessage="Le nom de la ville'{{ value }}' est trop long")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     *
     */
    private $IPAdress;

    /**
     * @ORM\Column(type="boolean")
     */
    private $level;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="Member")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="member")
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentLike", mappedBy="member")
     */
    private $commentLikes;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->commentLikes = new ArrayCollection();
        $this->roles = ['ROLE_MEMBER'];
    }

    

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;

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

    public function getIPAdress(): ?string
    {
        return $this->IPAdress;
    }

    public function setIPAdress(?string $IPAdress): self
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setMember($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getMember() === $this) {
                $game->setMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setMember($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getMember() === $this) {
                $note->setMember(null);
            }
        }

        return $this;
    }
}
