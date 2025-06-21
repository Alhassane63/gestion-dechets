<?php

namespace App\Entity;

use App\Repository\PointCollecteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointCollecteRepository::class)]
class PointCollecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255, options: ["default" => "actif"])]
    private ?string $statut = 'actif';

    #[ORM\ManyToOne(targetEntity: Tournee::class, inversedBy: 'pointCollectes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournee $tournee = null;

    #[ORM\Column(length: 255)]
    private ?string $typeDechets = null;

    #[ORM\Column(type: 'float')]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float')]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // Ajout de la relation avec Zone
    #[ORM\ManyToOne(targetEntity: Zone::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Zone $zone = null;

    /**
     * @var Collection<int, ProblemeSignale>
     */
    #[ORM\OneToMany(targetEntity: ProblemeSignale::class, mappedBy: 'point')]
    private Collection $problemeSignales;

    /**
     * @var Collection<int, Collecte>
     */
    #[ORM\OneToMany(targetEntity: Collecte::class, mappedBy: 'pointCollecte')]
    private Collection $collectes;

    public function __construct()
    {
        $this->problemeSignales = new ArrayCollection();
        $this->collectes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): static
    {
        $this->Adresse = $Adresse;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getTournee(): ?Tournee
    {
        return $this->tournee;
    }

    public function setTournee(?Tournee $tournee): static
    {
        $this->tournee = $tournee;
        return $this;
    }

    public function getTypeDechets(): ?string
    {
        return $this->typeDechets;
    }

    public function setTypeDechets(string $typeDechets): static
    {
        $this->typeDechets = $typeDechets;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Getter et Setter pour Zone
    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * @return Collection<int, ProblemeSignale>
     */
    public function getProblemeSignales(): Collection
    {
        return $this->problemeSignales;
    }

    public function addProblemeSignale(ProblemeSignale $problemeSignale): static
    {
        if (!$this->problemeSignales->contains($problemeSignale)) {
            $this->problemeSignales->add($problemeSignale);
            $problemeSignale->setPoint($this);
        }
        return $this;
    }

    public function removeProblemeSignale(ProblemeSignale $problemeSignale): static
    {
        if ($this->problemeSignales->removeElement($problemeSignale)) {
            if ($problemeSignale->getPoint() === $this) {
                $problemeSignale->setPoint(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Collecte>
     */
    public function getCollectes(): Collection
    {
        return $this->collectes;
    }

    public function addCollecte(Collecte $collecte): static
    {
        if (!$this->collectes->contains($collecte)) {
            $this->collectes->add($collecte);
            $collecte->setPointCollecte($this);
        }
        return $this;
    }

    public function removeCollecte(Collecte $collecte): static
    {
        if ($this->collectes->removeElement($collecte)) {
            if ($collecte->getPointCollecte() === $this) {
                $collecte->setPointCollecte(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->Adresse ?? '';
    }
}