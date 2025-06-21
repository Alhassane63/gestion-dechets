<?php

namespace App\Entity;

use App\Entity\Dechet;
use App\Entity\Signalement;
use App\Entity\Collecte;
use App\Repository\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
#[ORM\Index(columns: ['nom'])]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Quartier = null;

    #[ORM\Column(type: 'integer')]
    private ?int $Classement = null;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Dechet::class, orphanRemoval: true)]
    private Collection $dechets;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Signalement::class)]
    private Collection $signalements;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Collecte::class, orphanRemoval: true)]
    private Collection $collectes;

    // Ajout de la relation inverse avec PointCollecte
    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: PointCollecte::class)]
    private Collection $pointCollectes;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    public function __construct()
    {
        $this->dechets = new ArrayCollection();
        $this->signalements = new ArrayCollection();
        $this->collectes = new ArrayCollection();
        $this->pointCollectes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    public function getDechets(): Collection
    {
        return $this->dechets;
    }

    public function addDechet(Dechet $dechet): static
    {
        if (!$this->dechets->contains($dechet)) {
            $this->dechets->add($dechet);
            $dechet->setZone($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechets->removeElement($dechet)) {
            if ($dechet->getZone() === $this) {
                $dechet->setZone(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->Quartier;
    }

    public function setQuartier(string $Quartier): static
    {
        $this->Quartier = $Quartier;
        return $this;
    }

    public function getClassement(): ?int
    {
        return $this->Classement;
    }

    public function setClassement(int $Classement): static
    {
        $this->Classement = $Classement;
        return $this;
    }

    /**
     * @return Collection<int, Signalement>
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): static
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements->add($signalement);
            $signalement->setZone($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): static
    {
        if ($this->signalements->removeElement($signalement)) {
            if ($signalement->getZone() === $this) {
                $signalement->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Collecte>
     */
    public function getCollectes(): Collection
    {
        // Sécurisation de l'accès à la propriété
        if (!isset($this->collectes)) {
            $this->collectes = new ArrayCollection();
        }

        return $this->collectes;
    }

    public function addCollecte(Collecte $collecte): static
    {
        if (!$this->collectes->contains($collecte)) {
            $this->collectes->add($collecte);
            $collecte->setZone($this);
        }

        return $this;
    }

    public function removeCollecte(Collecte $collecte): static
    {
        if ($this->collectes->removeElement($collecte)) {
            if ($collecte->getZone() === $this) {
                $collecte->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PointCollecte>
     */
    public function getPointCollectes(): Collection
    {
        return $this->pointCollectes;
    }

    public function addPointCollecte(PointCollecte $pointCollecte): static
    {
        if (!$this->pointCollectes->contains($pointCollecte)) {
            $this->pointCollectes->add($pointCollecte);
            $pointCollecte->setZone($this);
        }

        return $this;
    }

    public function removePointCollecte(PointCollecte $pointCollecte): static
    {
        if ($this->pointCollectes->removeElement($pointCollecte)) {
            if ($pointCollecte->getZone() === $this) {
                $pointCollecte->setZone(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }
}