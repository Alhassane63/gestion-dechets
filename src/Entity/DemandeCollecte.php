<?php

namespace App\Entity;

use App\Repository\DemandeCollecteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeCollecteRepository::class)]
class DemandeCollecte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandeCollectes')]
    private ?user $Citoyen = null;

    #[ORM\Column]
    private ?\DateTime $dateDemande = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCitoyen(): ?user
    {
        return $this->Citoyen;
    }

    public function setCitoyen(?user $Citoyen): static
    {
        $this->Citoyen = $Citoyen;

        return $this;
    }

    public function getDateDemande(): ?\DateTime
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTime $dateDemande): static
    {
        $this->dateDemande = $dateDemande;

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
