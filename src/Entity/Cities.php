<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CitiesRepository")
 */
class Cities
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Canton", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $canton;


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

    public function getCanton(): ?Canton
    {
        return $this->canton;
    }

    public function setCanton(?Canton $canton): self
    {
        $this->canton = $canton;

        return $this;
    }

}
