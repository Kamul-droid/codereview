<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Mark::class, mappedBy="fscore", cascade={"persist", "remove"})
     */
    private $mark;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ufile")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $langage;

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

    public function getMark(): ?Mark
    {
        return $this->mark;
    }

    public function setMark(?Mark $mark): self
    {
        // unset the owning side of the relation if necessary
        if ($mark === null && $this->mark !== null) {
            $this->mark->setFscore(null);
        }

        // set the owning side of the relation if necessary
        if ($mark !== null && $mark->getFscore() !== $this) {
            $mark->setFscore($this);
        }

        $this->mark = $mark;

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

    public function getLangage(): ?string
    {
        return $this->langage;
    }

    public function setLangage(string $langage): self
    {
        $this->langage = $langage;

        return $this;
    }
}
