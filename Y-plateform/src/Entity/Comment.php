<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Error;

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
     * @Error\Length(min=3, max=255, minMessage="ton commentaire '{{ value }}' est trop court", 
     * maxMessage="Ton commentaire '{{ value }}' est trop long")
     */
    private $description_c;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_c;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="comments")
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentLike", mappedBy="comment")
     */
    private $commentLikes;

    public function __construct()
    {
        $this->commentLikes = new ArrayCollection();
    }

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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

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

    /**
     * @return Collection|CommentLike[]
     */
    public function getCommentLikes(): Collection
    {
        return $this->commentLikes;
    }

    public function addCommentLike(CommentLike $commentLike): self
    {
        if (!$this->commentLikes->contains($commentLike)) {
            $this->commentLikes[] = $commentLike;
            $commentLike->setComment($this);
        }

        return $this;
    }

    public function removeCommentLike(CommentLike $commentLike): self
    {
        if ($this->commentLikes->contains($commentLike)) {
            $this->commentLikes->removeElement($commentLike);
            // set the owning side to null (unless already changed)
            if ($commentLike->getComment() === $this) {
                $commentLike->setComment(null);
            }
        }

        return $this;
    }

     /**permet de savoir si un user a liké un post */
     public function isLikedByUser(User $user) : bool {
        foreach ($this->commentLikes as $like) {
            if($like->getUser() === $user){
                return true;
            } 
        }
        return false;
    }

     /**permet de savoir si un user a liké ou dislike */
     public function likeOrDislike(User $user) {
        foreach ($this->commentLikes as $like) {
            if($like->getUser() === $user){
                if($like -> getValue() === true){
                    return true;
                } else{
                    return false;
                }
            } 
        }
    }
}
