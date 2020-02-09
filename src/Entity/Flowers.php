<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlowersRepository")
 */
class Flowers
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
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Img", mappedBy="flower", cascade={"persist", "remove"})
     */
    private $img;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?Img
    {
        return $this->img;
    }

    public function setImg(?Img $img): self
    {
        $this->img = $img;

        // set (or unset) the owning side of the relation if necessary
        $newFlower = null === $img ? null : $this;
        if ($img->getFlower() !== $newFlower) {
            $img->setFlower($newFlower);
        }

        return $this;
    }
}
