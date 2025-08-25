<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_equipe = null;

    // Utiliser un tableau pour les compétences
    #[ORM\Column(type: "json")]
    private array $competance_equipe = []; // Cela stockera un tableau JSON dans la base de données

    #[ORM\Column]
    private ?int $nombre = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $chef_equipe = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'equipe')]
    private Collection $ouvriers;

    #[ORM\OneToMany(targetEntity: Chantier::class, mappedBy: 'equipe')]
    private Collection $chantiers;

    public function __construct()
    {
        $this->ouvriers = new ArrayCollection();
        $this->chantiers = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNomEquipe(): ?string { return $this->nom_equipe; }
    public function setNomEquipe(string $nom_equipe): static { $this->nom_equipe = $nom_equipe; return $this; }

    // Getter et Setter pour competance_equipe
    public function getCompetanceEquipe(): array { return $this->competance_equipe; }

    public function setCompetanceEquipe(array $competance_equipe): static { 
        $this->competance_equipe = $competance_equipe; 
        return $this; 
    }

    public function getNombre(): ?int { return $this->nombre; }
    public function setNombre(int $nombre): static { $this->nombre = $nombre; return $this; }

    public function getChefEquipe(): ?User { return $this->chef_equipe; }
    public function setChefEquipe(?User $chef_equipe): static { $this->chef_equipe = $chef_equipe; return $this; }

    /**
     * @return Collection<int, User>
     */
    public function getOuvriers(): Collection { return $this->ouvriers; }

    public function addOuvrier(User $ouvrier): static 
    {
        if (!$this->ouvriers->contains($ouvrier)) {
            $this->ouvriers->add($ouvrier);
            $ouvrier->setEquipe($this);
        }
        return $this;
    }

    public function removeOuvrier(User $ouvrier): static 
    {
        if ($this->ouvriers->removeElement($ouvrier) && $ouvrier->getEquipe() === $this) {
            $ouvrier->setEquipe(null);
        }
        return $this;
    }

    /**
     * @return Collection<int, Chantier>
     */
    public function getChantiers(): Collection { return $this->chantiers; }

    public function addChantier(Chantier $chantier): static 
    {
        if (!$this->chantiers->contains($chantier)) {
            $this->chantiers->add($chantier);
            $chantier->setEquipe($this);
        }
        return $this;
    }

    public function removeChantier(Chantier $chantier): static 
    {
        if ($this->chantiers->removeElement($chantier) && $chantier->getEquipe() === $this) {
            $chantier->setEquipe(null);
        }
        return $this;
    }
}