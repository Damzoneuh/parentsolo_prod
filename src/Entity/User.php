<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValidated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentProfil", mappedBy="user")
     */
    private $payment_profil;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Img", mappedBy="user")
     */
    private $img;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profil", cascade={"persist", "remove"})
     */
    private $profil;

    /**
     * @ORM\Column(type="integer")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Subscribe", cascade={"persist", "remove"})
     */
    private $subscribe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Items", inversedBy="users")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="user")
     */
    private $payments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $flowerNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $favoriteNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isConfirmed;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FlowerReceived", mappedBy="target")
     */
    private $flowerReceiveds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FlowerReceived", mappedBy="target", orphanRemoval=true)
     */
    private $target;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FlowerReceived", mappedBy="sender", orphanRemoval=true)
     */
    private $sender;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Groups", mappedBy="members")
     */
    private $groupsMembers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $IsLookingSex;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $npa;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $LangForModeration;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isNotified;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Visit", mappedBy="target")
     */
    private $visits;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCalled;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Comment", inversedBy="users")
     */
    private $comments;

    public function __construct()
    {
        $this->payment_profil = new ArrayCollection();
        $this->img = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->flowerReceiveds = new ArrayCollection();
        $this->target = new ArrayCollection();
        $this->sender = new ArrayCollection();
        $this->groupsMembers = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return Collection|PaymentProfil[]
     */
    public function getPaymentProfil(): Collection
    {
        return $this->payment_profil;
    }

    public function addPaymentProfil(PaymentProfil $paymentProfil): self
    {
        if (!$this->payment_profil->contains($paymentProfil)) {
            $this->payment_profil[] = $paymentProfil;
            $paymentProfil->setUser($this);
        }

        return $this;
    }

    public function removePaymentProfil(PaymentProfil $paymentProfil): self
    {
        if ($this->payment_profil->contains($paymentProfil)) {
            $this->payment_profil->removeElement($paymentProfil);
            // set the owning side to null (unless already changed)
            if ($paymentProfil->getUser() === $this) {
                $paymentProfil->setUser(null);
            }
        }

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
            $img->setUser($this);
        }

        return $this;
    }

    public function removeImg(Img $img): self
    {
        if ($this->img->contains($img)) {
            $this->img->removeElement($img);
            // set the owning side to null (unless already changed)
            if ($img->getUser() === $this) {
                $img->setUser(null);
            }
        }

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

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Items $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function removeItem(Items $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

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
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    public function getFlowerNumber(): ?int
    {
        return $this->flowerNumber;
    }

    public function setFlowerNumber(?int $flowerNumber): self
    {
        $this->flowerNumber = $flowerNumber;

        return $this;
    }

    public function getFavoriteNumber(): ?int
    {
        return $this->favoriteNumber;
    }

    public function setFavoriteNumber(?int $favoriteNumber): self
    {
        $this->favoriteNumber = $favoriteNumber;

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            $message->removeMessageTo($this);
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * @return Collection|FlowerReceived[]
     */
    public function getFlowerReceiveds(): Collection
    {
        return $this->flowerReceiveds;
    }

    public function addFlowerReceived(FlowerReceived $flowerReceived): self
    {
        if (!$this->flowerReceiveds->contains($flowerReceived)) {
            $this->flowerReceiveds[] = $flowerReceived;
            $flowerReceived->addTarget($this);
        }

        return $this;
    }

    public function removeFlowerReceived(FlowerReceived $flowerReceived): self
    {
        if ($this->flowerReceiveds->contains($flowerReceived)) {
            $this->flowerReceiveds->removeElement($flowerReceived);
            $flowerReceived->removeTarget($this);
        }

        return $this;
    }

    /**
     * @return Collection|FlowerReceived[]
     */
    public function getTarget(): Collection
    {
        return $this->target;
    }

    public function addTarget(FlowerReceived $target): self
    {
        if (!$this->target->contains($target)) {
            $this->target[] = $target;
            $target->setTarget($this);
        }

        return $this;
    }

    public function removeTarget(FlowerReceived $target): self
    {
        if ($this->target->contains($target)) {
            $this->target->removeElement($target);
            // set the owning side to null (unless already changed)
            if ($target->getTarget() === $this) {
                $target->setTarget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FlowerReceived[]
     */
    public function getSender(): Collection
    {
        return $this->sender;
    }

    public function addSender(FlowerReceived $sender): self
    {
        if (!$this->sender->contains($sender)) {
            $this->sender[] = $sender;
            $sender->setSender($this);
        }

        return $this;
    }

    public function removeSender(FlowerReceived $sender): self
    {
        if ($this->sender->contains($sender)) {
            $this->sender->removeElement($sender);
            // set the owning side to null (unless already changed)
            if ($sender->getSender() === $this) {
                $sender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Groups[]
     */
    public function getGroupsMembers(): Collection
    {
        return $this->groupsMembers;
    }

    public function addGroupsMember(Groups $groupsMember): self
    {
        if (!$this->groupsMembers->contains($groupsMember)) {
            $this->groupsMembers[] = $groupsMember;
            $groupsMember->addMember($this);
        }

        return $this;
    }

    public function removeGroupsMember(Groups $groupsMember): self
    {
        if ($this->groupsMembers->contains($groupsMember)) {
            $this->groupsMembers->removeElement($groupsMember);
            $groupsMember->removeMember($this);
        }

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getIsLookingSex(): ?bool
    {
        return $this->IsLookingSex;
    }

    public function setIsLookingSex(?bool $IsLookingSex): self
    {
        $this->IsLookingSex = $IsLookingSex;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(?string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getNpa(): ?int
    {
        return $this->npa;
    }

    public function setNpa(?int $npa): self
    {
        $this->npa = $npa;

        return $this;
    }

    public function getLangForModeration(): ?string
    {
        return $this->LangForModeration;
    }

    public function setLangForModeration(?string $LangForModeration): self
    {
        $this->LangForModeration = $LangForModeration;

        return $this;
    }

    public function getIsNotified(): ?bool
    {
        return $this->isNotified;
    }

    public function setIsNotified(?bool $isNotified): self
    {
        $this->isNotified = $isNotified;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|Visit[]
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits[] = $visit;
            $visit->addTarget($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->contains($visit)) {
            $this->visits->removeElement($visit);
            $visit->removeTarget($this);
        }

        return $this;
    }

    public function getIsCalled(): ?bool
    {
        return $this->isCalled;
    }

    public function setIsCalled(?bool $isCalled): self
    {
        $this->isCalled = $isCalled;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
        }

        return $this;
    }
}
