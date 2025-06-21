<?php

namespace App\Entity;

use App\Repository\CollecteRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Zone;
use App\Entity\User;
use App\Entity\PointCollecte;

#[ORM\Entity(repositoryClass: CollecteRepository::class)]
class Collecte
{
    public const STATUTS = [
        'en_attente' => 'En attente',
        'en_cours' => 'En cours',
        'effectuee' => 'Effectuée',
        'annulee' => 'Annulée'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'collectes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Zone $zone = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $agent = null;

    #[ORM\ManyToOne(targetEntity: PointCollecte::class, inversedBy: 'collectes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PointCollecte $pointCollecte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'boolean')]
    private bool $collecteEffectuee = false;

    #[ORM\Column(length: 255)]
    private ?string $typeDechets = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = 'en_attente';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;
        return $this;
    }

    public function getAgent(): ?User
    {
        return $this->agent;
    }

    public function setAgent(?User $agent): self
    {
        $this->agent = $agent;
        return $this;
    }

    public function getPointCollecte(): ?PointCollecte
    {
        return $this->pointCollecte;
    }

    public function setPointCollecte(?PointCollecte $pointCollecte): self
    {
        $this->pointCollecte = $pointCollecte;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function isCollecteEffectuee(): bool
    {
        return $this->collecteEffectuee;
    }

    public function setCollecteEffectuee(bool $collecteEffectuee): self
    {
        $this->collecteEffectuee = $collecteEffectuee;
        return $this;
    }

    public function getTypeDechets(): ?string
    {
        return $this->typeDechets;
    }

    public function setTypeDechets(?string $typeDechets): self
    {
        $this->typeDechets = $typeDechets;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function isValidStatut(string $statut): bool
    {
        return array_key_exists($statut, self::STATUTS);
    }

    public function __construct()
    {
        $this->collecteEffectuee = false;
        $this->statut = 'en_attente';
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return sprintf('Collecte #%d', 
            $this->id ?? 0
        );
    }
}
