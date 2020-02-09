<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginRepository")
 */
class Origin
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
     * @ORM\OneToOne(targetEntity="App\Entity\Profil", mappedBy="origin", cascade={"persist", "remove"} )
     */
    private $profil;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        // set (or unset) the owning side of the relation if necessary
        $newOrigiin = $profil === null ? null : $this;
        if ($newOrigiin !== $profil->getOrigiin()) {
            $profil->setOrigiin($newOrigiin);
        }

        return $this;
    }
}
