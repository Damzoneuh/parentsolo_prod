<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscribeRepository")
 */
class Subscribe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deadline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plan;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAuthorized;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="subscribe")
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Items")
     */
    private $item;

    public function __construct()
    {
        $this->payment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(?string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getIsAuthorized(): ?bool
    {
        return $this->isAuthorized;
    }

    public function setIsAuthorized(bool $isAuthorized): self
    {
        $this->isAuthorized = $isAuthorized;

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayment(): Collection
    {
        return $this->payment;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payment->contains($payment)) {
            $this->payment[] = $payment;
            $payment->setSubscribe($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payment->contains($payment)) {
            $this->payment->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getSubscribe() === $this) {
                $payment->setSubscribe(null);
            }
        }

        return $this;
    }

    public function getItem(): ?Items
    {
        return $this->item;
    }

    public function setItem(?Items $item): self
    {
        $this->item = $item;

        return $this;
    }
}
