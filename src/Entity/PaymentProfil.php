<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentProfilRepository")
 */
class PaymentProfil
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
    private $alias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="payment_profil")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $card_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $display_text;

    /**
     * @ORM\Column(type="integer")
     */
    private $exp_month;

    /**
     * @ORM\Column(type="integer")
     */
    private $exp_year;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selected;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="payment_profil")
     */
    private $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

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

    public function getCardName(): ?string
    {
        return $this->card_name;
    }

    public function setCardName(string $card_name): self
    {
        $this->card_name = $card_name;

        return $this;
    }

    public function getDisplayText(): ?string
    {
        return $this->display_text;
    }

    public function setDisplayText(string $display_text): self
    {
        $this->display_text = $display_text;

        return $this;
    }

    public function getExpMonth(): ?int
    {
        return $this->exp_month;
    }

    public function setExpMonth(int $exp_month): self
    {
        $this->exp_month = $exp_month;

        return $this;
    }

    public function getExpYear(): ?int
    {
        return $this->exp_year;
    }

    public function setExpYear(int $exp_year): self
    {
        $this->exp_year = $exp_year;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setPaymentProfil($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getPaymentProfil() === $this) {
                $payment->setPaymentProfil(null);
            }
        }

        return $this;
    }
}
