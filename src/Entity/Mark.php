<?php

namespace App\Entity;

use App\Repository\MarkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarkRepository::class)
 */
class Mark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

  
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $started_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="marks")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Exercice::class, inversedBy="marks")
     */
    private $exo;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

  

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeImmutable $started_at): self
    {
        $this->started_at = $started_at;

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

    public function getExo(): ?Exercice
    {
        return $this->exo;
    }

    public function setExo(?Exercice $exo): self
    {
        $this->exo = $exo;

        return $this;
    }


}
