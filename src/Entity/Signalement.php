<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
#[ORM\Index(columns: ['lieu'])]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[ORM\UniqueEntity(fields: ['lieu', 'dateSignalement', 'zone'])]
    private ?string $lieu = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeInterface $dateSignalement = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'signalements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $citoyen = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = 'nouveau';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateTraitement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaireTraitement = null;

    #[ORM\ManyToOne(inversedBy: 'signalements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Zone $zone = null;

    public function __construct()
    {
        $this->dateSignalement = new \DateTimeImmutable();
        $this->etat = 'nouveau';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getDateSignalement(): ?DateTimeInterface
    {
        return $this->dateSignalement;
    }

    public function setDateSignalement(DateTimeInterface $dateSignalement): static
    {
        $this->dateSignalement = $dateSignalement;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;
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

    // ✅ Ajout manquant : Getter et setter pour statut
    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    // ✅ Getter et setter pour citoyen
    public function getCitoyen(): ?User
    {
        return $this->citoyen;
    }

    public function setCitoyen(?User $citoyen): static
    {
        $this->citoyen = $citoyen;
        return $this;
    }

    // ✅ Getter et setter pour dateTraitement
    public function getDateTraitement(): ?\DateTimeInterface
    {
        return $this->dateTraitement;
    }

    public function setDateTraitement(?\DateTimeInterface $dateTraitement): static
    {
        $this->dateTraitement = $dateTraitement;
        return $this;
    }

    // ✅ Getter et setter pour commentaireTraitement
    public function getCommentaireTraitement(): ?string
    {
        return $this->commentaireTraitement;
    }

    public function setCommentaireTraitement(?string $commentaireTraitement): static
    {
        $this->commentaireTraitement = $commentaireTraitement;
        return $this;
    }
}
