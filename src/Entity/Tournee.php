<?php

namespace App\Entity;

use App\Repository\TourneeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourneeRepository::class)]
class Tournee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    /**
     * @var Collection<int, PointCollecte>
     */
    #[ORM\OneToMany(targetEntity: PointCollecte::class, mappedBy: 'tournee')]
    private Collection $pointCollectes;

    public function __construct()
    {
        $this->pointCollectes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

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
            $pointCollecte->setTournee($this);
        }

        return $this;
    }

    public function removePointCollecte(PointCollecte $pointCollecte): static
    {
        if ($this->pointCollectes->removeElement($pointCollecte)) {
            // set the owning side to null (unless already changed)
            if ($pointCollecte->getTournee() === $this) {
                $pointCollecte->setTournee(null);
            }
        }

        return $this;
    }
}
