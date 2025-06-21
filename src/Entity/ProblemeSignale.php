<?php

namespace App\Entity;

use App\Repository\ProblemeSignaleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProblemeSignaleRepository::class)]
class ProblemeSignale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'problemeSignales')]
    private ?user $Utilisateur = null;

    #[ORM\Column]
    private ?\DateTime $dateSignal = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'problemeSignales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PointCollecte $point = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?user
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?user $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }

    public function getDateSignal(): ?\DateTime
    {
        return $this->dateSignal;
    }

    public function setDateSignal(\DateTime $dateSignal): static
    {
        $this->dateSignal = $dateSignal;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getPoint(): ?PointCollecte
    {
        return $this->point;
    }

    public function setPoint(?PointCollecte $point): static
    {
        $this->point = $point;

        return $this;
    }
}
