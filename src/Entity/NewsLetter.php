<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsLetterRepository")
 */
class NewsLetter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $deTitle;

    /**
     * @ORM\Column(type="text")
     */
    private $deText;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $frTitle;

    /**
     * @ORM\Column(type="text")
     */
    private $frText;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $enTitle;

    /**
     * @ORM\Column(type="text")
     */
    private $enText;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeTitle(): ?string
    {
        return $this->deTitle;
    }

    public function setDeTitle(string $deTitle): self
    {
        $this->deTitle = $deTitle;

        return $this;
    }

    public function getDeText(): ?string
    {
        return $this->deText;
    }

    public function setDeText(string $deText): self
    {
        $this->deText = $deText;

        return $this;
    }

    public function getFrTitle(): ?string
    {
        return $this->frTitle;
    }

    public function setFrTitle(string $frTitle): self
    {
        $this->frTitle = $frTitle;

        return $this;
    }

    public function getFrText(): ?string
    {
        return $this->frText;
    }

    public function setFrText(string $frText): self
    {
        $this->frText = $frText;

        return $this;
    }

    public function getEnTitle(): ?string
    {
        return $this->enTitle;
    }

    public function setEnTitle(string $enTitle): self
    {
        $this->enTitle = $enTitle;

        return $this;
    }

    public function getEnText(): ?string
    {
        return $this->enText;
    }

    public function setEnText(string $enText): self
    {
        $this->enText = $enText;

        return $this;
    }
}
