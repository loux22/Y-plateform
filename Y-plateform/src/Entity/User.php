<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Error;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="mail", message="Le mail existe déja")
 * @UniqueEntity(fields="pseudo", message="le pseudo existe deja")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Error\Length(min=3, max=30, minMessage="ton username '{{ value }}' est trop court", 
     * maxMessage="Ton username '{{ value }}' est trop long")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=30)
     * @Error\Length(min=3, max=30, minMessage="Ton lastname '{{ value }}' est trop court", 
     * maxMessage="Ton lastname '{{ value }}' est trop long") 
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     * Error\Email(
     *     message = "Ton Email '{{ value }}' n'est pas valide.")
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     * @Error\length(min="8", minMessage="ton mot de passe doit contenir au moins 8 caractere")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=50)
     * @Error\Length(min=3, max=30, minMessage="ton username '{{ value }}' est trop court", 
     * maxMessage="Ton username '{{ value }}' est trop long")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="date")
     */
    private $date_u;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive_u;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $age;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getusername(): ?string
    {
        return $this->username;
    }

    public function setusername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
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

    public function getDateU(): ?\DateTimeInterface
    {
        return $this->date_u;
    }

    public function setDateU(\DateTimeInterface $date_u): self
    {
        $this->date_u = $date_u;

        return $this;
    }

    public function getIsActiveU(): ?bool
    {
        return $this->isActive_u;
    }

    public function setIsActiveU(bool $isActive_u): self
    {
        $this->isActive_u = $isActive_u;

        return $this;
    }

    public function getRoles(){
        return ['ROLE_USER'];
    }

    public function eraseCredentials(){

    }

    public function getSalt()
    {
        return null;
    }

    public function getAge(): ?\DateTimeInterface
    {
        return $this->age;
    }

    public function setAge(?\DateTimeInterface $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function removeFile()
    {
        if(file_exists('/../../public/avatar/' . $this-> avatar) && $this-> avatar != '0.png'){
            unlink('/../../public/avatar/' . $this-> avatar);
        }
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
