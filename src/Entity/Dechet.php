<?php

namespace App\Entity;

use App\Entity\Zone;
use App\Entity\User;
use App\Repository\DechetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DechetRepository::class)]
#[ORM\Table(name: '`dechet`')]
class Dechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $quantite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'dechets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $citoyen = null;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'dechets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Zone $zone = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $date = null;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getCitoyen(): ?User
    {
        return $this->citoyen;
    }

    public function setCitoyen(?User $citoyen): static
    {
        $this->citoyen = $citoyen;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s kg', 
            $this->type ?? 'Type non défini', 
            $this->quantite ?? 0
        );
    }
}