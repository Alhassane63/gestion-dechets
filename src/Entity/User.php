<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Signalement;
use App\Entity\Collecte;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'email_unique', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'citoyen', targetEntity: Dechet::class)]
    private Collection $dechets;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_AGENT = 'ROLE_AGENT';
    const ROLE_CITOYEN = 'ROLE_CITOYEN';

    #[ORM\OneToMany(mappedBy: 'citoyen', targetEntity: Signalement::class)]
    private Collection $signalements;

    #[ORM\OneToMany(targetEntity: DemandeCollecte::class, mappedBy: 'Citoyen')]
    private Collection $demandeCollectes;

    #[ORM\OneToMany(targetEntity: ProblemeSignale::class, mappedBy: 'Utilisateur')]
    private Collection $problemeSignales;

    // Ajout ici de la relation manquante pour Collecte
    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: Collecte::class)]
    private Collection $collectes;

    public function __construct()
    {
        $this->roles = ['ROLE_CITOYEN'];
        $this->dechets = new ArrayCollection();
        $this->signalements = new ArrayCollection();
        $this->demandeCollectes = new ArrayCollection();
        $this->problemeSignales = new ArrayCollection();
        $this->collectes = new ArrayCollection(); // Initialisation obligatoire
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setRole(string $role): static
    {
        $this->roles = [$role];
        return $this;
    }

    public function getRole(): string
    {
        return $this->roles[0] ?? '';
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): static
    {
        $this->matricule = $matricule;
        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): static
    {
        $this->dateEmbauche = $dateEmbauche;
        return $this;
    }

    public function getDechets(): Collection
    {
        return $this->dechets;
    }

    public function addDechet(Dechet $dechet): static
    {
        if (!$this->dechets->contains($dechet)) {
            $this->dechets->add($dechet);
            $dechet->setCitoyen($this);
        }

        return $this;
    }

    public function removeDechet(Dechet $dechet): static
    {
        if ($this->dechets->removeElement($dechet)) {
            if ($dechet->getCitoyen() === $this) {
                $dechet->setCitoyen(null);
            }
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->roles);
    }

    public function isAgent(): bool
    {
        return in_array(self::ROLE_AGENT, $this->roles);
    }

    public function isCitoyen(): bool
    {
        return in_array(self::ROLE_CITOYEN, $this->roles);
    }

    public function eraseCredentials(): void
    {
        // Clear temporary sensitive data if any
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
            $signalement->setCitoyen($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): static
    {
        if ($this->signalements->removeElement($signalement)) {
            if ($signalement->getCitoyen() === $this) {
                $signalement->setCitoyen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeCollecte>
     */
    public function getDemandeCollectes(): Collection
    {
        return $this->demandeCollectes;
    }

    public function addDemandeCollecte(DemandeCollecte $demandeCollecte): static
    {
        if (!$this->demandeCollectes->contains($demandeCollecte)) {
            $this->demandeCollectes->add($demandeCollecte);
            $demandeCollecte->setCitoyen($this);
        }

        return $this;
    }

    public function removeDemandeCollecte(DemandeCollecte $demandeCollecte): static
    {
        if ($this->demandeCollectes->removeElement($demandeCollecte)) {
            if ($demandeCollecte->getCitoyen() === $this) {
                $demandeCollecte->setCitoyen(null);
            }
        }

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
            $problemeSignale->setUtilisateur($this);
        }

        return $this;
    }

    public function removeProblemeSignale(ProblemeSignale $problemeSignale): static
    {
        if ($this->problemeSignales->removeElement($problemeSignale)) {
            if ($problemeSignale->getUtilisateur() === $this) {
                $problemeSignale->setUtilisateur(null);
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
            $collecte->setAgent($this);
        }

        return $this;
    }

    public function removeCollecte(Collecte $collecte): static
    {
        if ($this->collectes->removeElement($collecte)) {
            if ($collecte->getAgent() === $this) {
                $collecte->setAgent(null);
            }
        }

        return $this;
    }
}
