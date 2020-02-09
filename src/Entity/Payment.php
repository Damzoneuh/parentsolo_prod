<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
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
    private $uniqKey;



    /**
     * @ORM\Column(type="boolean")
     */
    private $isCaptured;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $method;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentProfil", inversedBy="payments")
     */
    private $payment_profil;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="payments")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscribe", inversedBy="payment")
     */
    private $subscribe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Items")
     */
    private $item;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAccepted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->item = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqKey(): ?string
    {
        return $this->uniqKey;
    }

    public function setUniqKey(string $uniqKey): self
    {
        $this->uniqKey = $uniqKey;

        return $this;
    }

    public function getIsCaptured(): ?bool
    {
        return $this->isCaptured;
    }

    public function setIsCaptured(bool $isCaptured): self
    {
        $this->isCaptured = $isCaptured;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getPaymentProfil(): ?PaymentProfil
    {
        return $this->payment_profil;
    }

    public function setPaymentProfil(?PaymentProfil $payment_profil): self
    {
        $this->payment_profil = $payment_profil;

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

    public function getSubscribe(): ?Subscribe
    {
        return $this->subscribe;
    }

    public function setSubscribe(?Subscribe $subscribe): self
    {
        $this->subscribe = $subscribe;

        return $this;
    }

    /**
     * @return Collection|Items[]
     */
    public function getItem(): Collection
    {
        return $this->item;
    }

    public function addItem(Items $item): self
    {
        if (!$this->item->contains($item)) {
            $this->item[] = $item;
        }

        return $this;
    }

    public function removeItem(Items $item): self
    {
        if ($this->item->contains($item)) {
            $this->item->removeElement($item);
        }

        return $this;
    }

    public function getIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
