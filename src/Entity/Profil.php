<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfilRepository")
 */
class Profil
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_man;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Childs", mappedBy="profil")
     */
    private $childs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Nationality")
     */
    private $nationality;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Studies")
     */
    private $studies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LifeStyle")
     */
    private $lifestyle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ChildGard")
     */
    private $child_gard;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Smoke")
     */
    private $smoke;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Temperament")
     */
    private $temperament;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wedding")
     */
    private $wedding;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Size")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Eyes")
     */
    private $eyes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Origin")
     */
    private $origin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Weight")
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hair")
     */
    private $hair;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Silhouette")
     */
    private $silhouette;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HairStyle")
     */
    private $hair_style;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Cook", inversedBy="profils")
     */
    private $cook;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outing", inversedBy="profils")
     */
    private $outing;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hobbies", inversedBy="profils")
     */
    private $hobbies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sport", inversedBy="profils")
     */
    private $sports;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Music", inversedBy="profils")
     */
    private $music;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movies", inversedBy="profils")
     */
    private $movies;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Pets", inversedBy="profils")
     */
    private $pets;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Langages", mappedBy="profil")
     */
    private $langages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Religion")
     */
    private $religion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $childWanted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Relationship")
     */
    private $relation;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isLookingFor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profession")
     */
    private $profession;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cities")
     */
    private $city;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reading")
     */
    private $reading;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $favorite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FamilyStatus")
     */
    private $familyStatus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity")
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Description")
     */
    private $description;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
        $this->langages = new ArrayCollection();
        $this->cook = new ArrayCollection();
        $this->outing = new ArrayCollection();
        $this->hobbies = new ArrayCollection();
        $this->sports = new ArrayCollection();
        $this->music = new ArrayCollection();
        $this->movies = new ArrayCollection();
        $this->pets = new ArrayCollection();
        $this->reading = new ArrayCollection();
        $this->favorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsMan(): ?bool
    {
        return $this->is_man;
    }

    public function setIsMan(bool $is_man): self
    {
        $this->is_man = $is_man;

        return $this;
    }

    /**
     * @return Collection|Childs[]
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(Childs $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setProfil($this);
        }

        return $this;
    }

    public function removeChild(Childs $child): self
    {
        if ($this->childs->contains($child)) {
            $this->childs->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getProfil() === $this) {
                $child->setProfil(null);
            }
        }

        return $this;
    }

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getStudies(): ?Studies
    {
        return $this->studies;
    }

    public function setStudies(?Studies $studies): self
    {
        $this->studies = $studies;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLifestyle(): ?LifeStyle
    {
        return $this->lifestyle;
    }

    public function setLifestyle(?LifeStyle $lifestyle): self
    {
        $this->lifestyle = $lifestyle;

        return $this;
    }

    public function getChildGard(): ?ChildGard
    {
        return $this->child_gard;
    }

    public function setChildGard(?ChildGard $child_gard): self
    {
        $this->child_gard = $child_gard;

        return $this;
    }

    public function getSmoke(): ?Smoke
    {
        return $this->smoke;
    }

    public function setSmoke(?Smoke $smoke): self
    {
        $this->smoke = $smoke;

        return $this;
    }

    public function getTemperament(): ?Temperament
    {
        return $this->temperament;
    }

    public function setTemperament(?Temperament $temperament): self
    {
        $this->temperament = $temperament;

        return $this;
    }

    public function getWedding(): ?Wedding
    {
        return $this->wedding;
    }

    public function setWedding(?Wedding $wedding): self
    {
        $this->wedding = $wedding;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getEyes(): ?Eyes
    {
        return $this->eyes;
    }

    public function setEyes(?Eyes $eyes): self
    {
        $this->eyes = $eyes;

        return $this;
    }

    public function getorigin(): ?Origin
    {
        return $this->origin;
    }

    public function setorigin(?Origin $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWeight(): ?Weight
    {
        return $this->weight;
    }

    public function setWeight(?Weight $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHair(): ?Hair
    {
        return $this->hair;
    }

    public function setHair(?Hair $hair): self
    {
        $this->hair = $hair;

        return $this;
    }

    public function getSilhouette(): ?Silhouette
    {
        return $this->silhouette;
    }

    public function setSilhouette(?Silhouette $silhouette): self
    {
        $this->silhouette = $silhouette;

        return $this;
    }

    public function getHairStyle(): ?HairStyle
    {
        return $this->hair_style;
    }

    public function setHairStyle(?HairStyle $hair_style): self
    {
        $this->hair_style = $hair_style;

        return $this;
    }

    /**
     * @return Collection|Cook[]
     */
    public function getCook(): Collection
    {
        return $this->cook;
    }

    public function addCook(Cook $cook): self
    {
        if (!$this->cook->contains($cook)) {
            $this->cook[] = $cook;
        }

        return $this;
    }

    public function removeCook(Cook $cook): self
    {
        if ($this->cook->contains($cook)) {
            $this->cook->removeElement($cook);
        }

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOuting(): Collection
    {
        return $this->outing;
    }

    public function addOuting(Outing $outing): self
    {
        if (!$this->outing->contains($outing)) {
            $this->outing[] = $outing;
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        if ($this->outing->contains($outing)) {
            $this->outing->removeElement($outing);
        }

        return $this;
    }

    /**
     * @return Collection|Hobbies[]
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(Hobbies $hobby): self
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies[] = $hobby;
        }

        return $this;
    }

    public function removeHobby(Hobbies $hobby): self
    {
        if ($this->hobbies->contains($hobby)) {
            $this->hobbies->removeElement($hobby);
        }

        return $this;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(Sport $sport): self
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
        }

        return $this;
    }

    public function removeSport(Sport $sport): self
    {
        if ($this->sports->contains($sport)) {
            $this->sports->removeElement($sport);
        }

        return $this;
    }

    /**
     * @return Collection|Music[]
     */
    public function getMusic(): Collection
    {
        return $this->music;
    }

    public function addMusic(Music $music): self
    {
        if (!$this->music->contains($music)) {
            $this->music[] = $music;
        }

        return $this;
    }

    public function removeMusic(Music $music): self
    {
        if ($this->music->contains($music)) {
            $this->music->removeElement($music);
        }

        return $this;
    }

    /**
     * @return Collection|Movies[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movies $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movies $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }

        return $this;
    }


    /**
     * @return Collection|Pets[]
     */
    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function addPet(Pets $pet): self
    {
        if (!$this->pets->contains($pet)) {
            $this->pets[] = $pet;
        }

        return $this;
    }

    public function removePet(Pets $pet): self
    {
        if ($this->pets->contains($pet)) {
            $this->pets->removeElement($pet);
        }

        return $this;
    }

    /**
     * @return Collection|Langages[]
     */
    public function getLangages(): Collection
    {
        return $this->langages;
    }

    public function addLangage(Langages $langage): self
    {
        if (!$this->langages->contains($langage)) {
            $this->langages[] = $langage;
            $langage->addProfil($this);
        }

        return $this;
    }

    public function removeLangage(Langages $langage): self
    {
        if ($this->langages->contains($langage)) {
            $this->langages->removeElement($langage);
            $langage->removeProfil($this);
        }

        return $this;
    }

    public function getReligion(): ?Religion
    {
        return $this->religion;
    }

    public function setReligion(?Religion $religion): self
    {
        $this->religion = $religion;

        return $this;
    }

    public function getChildWanted(): ?int
    {
        return $this->childWanted;
    }

    public function setChildWanted(?int $childWanted): self
    {
        $this->childWanted = $childWanted;

        return $this;
    }

    public function getRelation(): ?Relationship
    {
        return $this->relation;
    }

    public function setRelation(?Relationship $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getIsLookingFor(): ?bool
    {
        return $this->isLookingFor;
    }

    public function setIsLookingFor(?bool $isLookingFor): self
    {
        $this->isLookingFor = $isLookingFor;

        return $this;
    }

    public function getProfession(): ?Profession
    {
        return $this->profession;
    }

    public function setProfession(?Profession $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getCity(): ?Cities
    {
        return $this->city;
    }

    public function setCity(?Cities $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|Reading[]
     */
    public function getReading(): Collection
    {
        return $this->reading;
    }

    public function addReading(Reading $reading): self
    {
        if (!$this->reading->contains($reading)) {
            $this->reading[] = $reading;
        }

        return $this;
    }

    public function removeReading(Reading $reading): self
    {
        if ($this->reading->contains($reading)) {
            $this->reading->removeElement($reading);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    public function addFavorite(User $favorite): self
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(User $favorite): self
    {
        if ($this->favorite->contains($favorite)) {
            $this->favorite->removeElement($favorite);
        }

        return $this;
    }

    public function getFamilyStatus(): ?FamilyStatus
    {
        return $this->familyStatus;
    }

    public function setFamilyStatus(?FamilyStatus $familyStatus): self
    {
        $this->familyStatus = $familyStatus;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDescription(): ?Description
    {
        return $this->description;
    }

    public function setDescription(?Description $description): self
    {
        $this->description = $description;

        return $this;
    }
}
