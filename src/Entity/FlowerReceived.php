<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlowerReceivedRepository")
 */
class FlowerReceived
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="target")
     * @ORM\JoinColumn(nullable=false)
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sender")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Flowers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $flower;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }
}
