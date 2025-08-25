<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    // Propriétés de l'ancien Ouvrier
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nom_ouvrier = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $role_ouvrier = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: "users")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?Equipe $equipe = null;

    // Nouvelle relation pour les compétences séparées
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserCompetence::class, cascade: ['persist', 'remove'])]
    private Collection $userCompetences;

    #[ORM\OneToMany(mappedBy: 'ouvrier', targetEntity: Affectation::class, orphanRemoval: true)]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
        $this->userCompetences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // Méthodes de l'ancien Ouvrier
    public function getNomOuvrier(): ?string
    {
        return $this->nom_ouvrier;
    }

    public function setNomOuvrier(?string $nom_ouvrier): static
    {
        $this->nom_ouvrier = $nom_ouvrier;
        return $this;
    }

    public function getRoleOuvrier(): ?string
    {
        return $this->role_ouvrier;
    }

    public function setRoleOuvrier(?string $role_ouvrier): static
    {
        $this->role_ouvrier = $role_ouvrier;
        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): static
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setOuvrier($this);
        }
        return $this;
    }

    public function removeAffectation(Affectation $affectation): static
    {
        if ($this->affectations->removeElement($affectation)) {
            if ($affectation->getOuvrier() === $this) {
                $affectation->setOuvrier(null);
            }
        }
        return $this;
    }

    // *** NOUVELLES MÉTHODES POUR LES COMPÉTENCES SÉPARÉES ***

    /**
     * @return Collection<int, UserCompetence>
     */
    public function getUserCompetences(): Collection
    {
        return $this->userCompetences;
    }

    public function addUserCompetence(UserCompetence $userCompetence): static
    {
        if (!$this->userCompetences->contains($userCompetence)) {
            $this->userCompetences->add($userCompetence);
            $userCompetence->setUser($this);
        }

        return $this;
    }

    public function removeUserCompetence(UserCompetence $userCompetence): static
    {
        if ($this->userCompetences->removeElement($userCompetence)) {
            if ($userCompetence->getUser() === $this) {
                $userCompetence->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Méthode utilitaire pour récupérer directement les compétences
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->userCompetences->map(function(UserCompetence $userCompetence) {
            return $userCompetence->getCompetence();
        });
    }

    /**
     * Vérifie si l'utilisateur a une compétence donnée
     */
    public function hasCompetence(Competence $competence): bool
    {
        foreach ($this->userCompetences as $userCompetence) {
            if ($userCompetence->getCompetence() === $competence) {
                return true;
            }
        }
        return false;
    }

    /**
     * Récupère le niveau d'une compétence donnée
     */
    public function getCompetenceLevel(Competence $competence): ?string
    {
        foreach ($this->userCompetences as $userCompetence) {
            if ($userCompetence->getCompetence() === $competence) {
                return $userCompetence->getNiveau();
            }
        }
        return null;
    }

    /**
     * Méthodes utilitaires pour vérifier le rôle
     */
    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isChef(): bool
    {
        return $this->role_ouvrier === 'Chef';
    }

    public function isOuvrier(): bool
    {
        return $this->role_ouvrier === 'Ouvrier';
    }

    // Méthode pour compatibilité avec l'ancien code
    public function getOuvrier(): ?self
    {
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom_ouvrier ? $this->nom_ouvrier . ' (' . $this->email . ')' : $this->email;
    }
}