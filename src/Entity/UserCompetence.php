<?php

namespace App\Entity;

use App\Repository\UserCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCompetenceRepository::class)]
class UserCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userCompetences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userCompetences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Competence $competence = null;

    #[ORM\Column(length: 20)]
    private string $niveau = 'DEBUTANT';

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateAttribution = null;

    #[ORM\ManyToOne]
    private ?User $attribueePar = null;

    public function __construct()
    {
        $this->dateAttribution = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): static
    {
        $this->competence = $competence;
        return $this;
    }

    public function getNiveau(): string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;
        return $this;
    }

    public function getDateAttribution(): ?\DateTimeImmutable
    {
        return $this->dateAttribution;
    }

    public function setDateAttribution(\DateTimeImmutable $dateAttribution): static
    {
        $this->dateAttribution = $dateAttribution;
        return $this;
    }

    public function getAttribueePar(): ?User
    {
        return $this->attribueePar;
    }

    public function setAttribueePar(?User $attribueePar): static
    {
        $this->attribueePar = $attribueePar;
        return $this;
    }

    // Méthodes utilitaires
    public function getNiveauLabel(): string
    {
        return match($this->niveau) {
            'DEBUTANT' => 'Débutant',
            'CONFIRME' => 'Confirmé',
            'EXPERT' => 'Expert',
            default => 'Débutant'
        };
    }

    public function getNiveauColor(): string
    {
        return match($this->niveau) {
            'DEBUTANT' => 'warning',
            'CONFIRME' => 'info',
            'EXPERT' => 'success',
            default => 'warning'
        };
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->user?->getNomOuvrier() ?? 'N/A',
            $this->competence?->getNom() ?? 'N/A',
            $this->getNiveauLabel()
        );
    }
}