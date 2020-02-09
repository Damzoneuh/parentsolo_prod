<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImgRepository")
 */
class Img
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
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="img")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groups", inversedBy="img")
     */
    private $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Childs", inversedBy="img")
     */
    private $childs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isProfile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Flowers", inversedBy="img", cascade={"persist", "remove"})
     */
    private $flower;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValidated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getGroups(): ?Groups
    {
        return $this->groups;
    }

    public function setGroups(?Groups $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getChilds(): ?Childs
    {
        return $this->childs;
    }

    public function setChilds(?Childs $childs): self
    {
        $this->childs = $childs;

        return $this;
    }

    public function getIsProfile(): ?bool
    {
        return $this->isProfile;
    }

    public function setIsProfile(bool $isProfile): self
    {
        $this->isProfile = $isProfile;

        return $this;
    }

    public function getFlower(): ?Flowers
    {
        return $this->flower;
    }

    public function setFlower(?Flowers $flower): self
    {
        $this->flower = $flower;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(?bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
