<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessagesRepository")
 */
class Messages
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
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $messageFrom;

    /**
     * @ORM\Column(type="integer")
     */
    private $messageTo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isClose;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMessageFrom(): ?int
    {
        return $this->messageFrom;
    }

    public function setMessageFrom(int $messageFrom): self
    {
        $this->messageFrom = $messageFrom;

        return $this;
    }

    public function getMessageTo(): ?int
    {
        return $this->messageTo;
    }

    public function setMessageTo(int $messageTo): self
    {
        $this->messageTo = $messageTo;

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

    public function getIsClose(): ?bool
    {
        return $this->isClose;
    }

    public function setIsClose(?bool $isClose): self
    {
        $this->isClose = $isClose;

        return $this;
    }
}
