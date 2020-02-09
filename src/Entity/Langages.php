<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LangagesRepository")
 */
class Langages
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Profil", inversedBy="langages")
     */
    private $profil;

    public function __construct()
    {
        $this->profil = new ArrayCollection();
    }

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

    /**
     * @return Collection|Profil[]
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profil->contains($profil)) {
            $this->profil->removeElement($profil);
        }

        return $this;
    }
}
