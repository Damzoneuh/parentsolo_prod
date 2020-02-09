<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildsRepository")
 */
class Childs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $born;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sex;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Img", mappedBy="childs")
     */
    private $img;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profil", inversedBy="childs")
     */
    private $profil;

    public function __construct()
    {
        $this->img = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorn(): ?\DateTimeInterface
    {
        return $this->born;
    }

    public function setBorn(?\DateTimeInterface $born): self
    {
        $this->born = $born;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return Collection|Img[]
     */
    public function getImg(): Collection
    {
        return $this->img;
    }

    public function addImg(Img $img): self
    {
        if (!$this->img->contains($img)) {
            $this->img[] = $img;
            $img->setChilds($this);
        }

        return $this;
    }

    public function removeImg(Img $img): self
    {
        if ($this->img->contains($img)) {
            $this->img->removeElement($img);
            // set the owning side to null (unless already changed)
            if ($img->getChilds() === $this) {
                $img->setChilds(null);
            }
        }

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }
}
