<?php

namespace App\Entity;

use App\Repository\AffectationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Equipe;
use App\Entity\Chantier;
use App\Entity\User;

#[ORM\Entity(repositoryClass: AffectationRepository::class)]
class Affectation
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'affectations')]
    private ?User $ouvrier = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: "affectations")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Equipe $equipe = null;

    #[ORM\ManyToOne(targetEntity: Chantier::class, inversedBy: "affectations")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Chantier $chantier = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    public function getId(): ?int 
    { 
        return $this->id; 
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

    public function getChantier(): ?Chantier 
    { 
        return $this->chantier; 
    }

    public function setChantier(?Chantier $chantier): static 
    { 
        $this->chantier = $chantier; 
        return $this; 
    }

    public function getDateDebut(): ?\DateTimeInterface 
    { 
        return $this->date_debut; 
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static 
    { 
        $this->date_debut = $date_debut; 
        return $this; 
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    
    public function getOuvrier(): ?User
    {
        return $this->ouvrier;
    }

    public function setOuvrier(?User $ouvrier): static
    {
        $this->ouvrier = $ouvrier;
        return $this;
    }
}