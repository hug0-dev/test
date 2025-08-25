<?php
namespace App\Entity;

    use App\Repository\ChantierRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Entity(repositoryClass: ChantierRepository::class)]
    class Chantier
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(length: 255)]
        private ?string $nom = null;

        #[ORM\Column(type: "json", nullable: true)]
        private ?array $chantier_prerequis = [];

        #[ORM\Column]
        private ?int $effectif_requis = null;

        #[ORM\Column(type: Types::DATE_MUTABLE)]
        private ?\DateTimeInterface $date_debut = null;

        #[ORM\Column(type: Types::DATE_MUTABLE)]
        private ?\DateTimeInterface $date_fin = null;

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(onDelete: "SET NULL")]
        private ?User $chef_chantier = null;

        #[ORM\OneToMany(mappedBy: 'chantier', targetEntity: Affectation::class, orphanRemoval: true)]
        private Collection $affectations;

        #[ORM\Column(length: 255, nullable: true)]
        private ?string $image = null;

        public function __construct() { $this->affectations = new ArrayCollection(); }

        public function getId(): ?int { return $this->id; }
        public function getNom(): ?string { return $this->nom; }
        public function setNom(string $nom): static { $this->nom = $nom; return $this; }

        public function getChantierPrerequis(): ?array
        {
            return $this->chantier_prerequis;
        }

        public function setChantierPrerequis(?array $chantier_prerequis): static
        {
            $this->chantier_prerequis = $chantier_prerequis;

            return $this;
        }

        public function getEffectifRequis(): ?int
        {
            return $this->effectif_requis;
        }

        public function setEffectifRequis(int $effectif_requis): static
        {
            $this->effectif_requis = $effectif_requis;

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

        public function getChefChantier(): ?User
        {
            return $this->chef_chantier;
        }

        public function setChefChantier(?User $chef_chantier): static
        {
            $this->chef_chantier = $chef_chantier;

            return $this;
        }

        /**
         * @return Collection<int, Affectation>
         */
        public function getAffectations(): Collection
        {
            return $this->affectations;
        }

        public function addAffectation(Affectation $affectation): static
        {
            if (!$this->affectations->contains($affectation)) {
                $this->affectations->add($affectation);
                $affectation->setChantier($this);
            }

            return $this;
        }

        public function removeAffectation(Affectation $affectation): static
        {
            if ($this->affectations->removeElement($affectation)) {
                if ($affectation->getChantier() === $this) {
                    $affectation->setChantier(null);
                }
            }
            return $this;
        }

        public function getImage(): ?string
        {
            return $this->image;
        }

        public function setImage(?string $image): static
        {
            $this->image = $image;

            return $this;
        }

        public function getEffectifRestant(): int
        {
            $totalAffecte = 0;
            foreach ($this->getAffectations() as $affectation) {
                $equipe = $affectation->getEquipe();
                if ($equipe && $equipe->getNombre()) {
                    $totalAffecte += $equipe->getNombre();
                }
            }
            
            $restant = $this->getEffectifRequis() - $totalAffecte;
            return ($restant > 0) ? $restant : 0;
        }
    }