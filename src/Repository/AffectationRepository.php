<?php

namespace App\Repository;

use App\Entity\Affectation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affectation>
 */
class AffectationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affectation::class);
    }

    /**
     * Trouve les affectations qui se chevauchent pour un ouvrier donné
     */
    public function findOverlappingAffectations(User $ouvrier, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.equipe', 'e')
            ->join('e.ouvriers', 'o')
            ->where('o = :ouvrier')
            ->andWhere('a.date_debut <= :dateFin AND a.date_fin >= :dateDebut')
            ->setParameter('ouvrier', $ouvrier)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les affectations d'un ouvrier
     */
    public function findByOuvrier(User $ouvrier): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.equipe', 'e')
            ->join('e.ouvriers', 'o')
            ->where('o = :ouvrier')
            ->setParameter('ouvrier', $ouvrier)
            ->orderBy('a.date_debut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les affectations en cours
     */
    public function findCurrentAffectations(): array
    {
        $today = new \DateTime();
        
        return $this->createQueryBuilder('a')
            ->where('a.date_debut <= :today AND a.date_fin >= :today')
            ->setParameter('today', $today)
            ->orderBy('a.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques pour admin
     */
    public function getStats(): array
    {
        $total = $this->count([]);
        
        $current = $this->findCurrentAffectations();
        
        $upcoming = $this->createQueryBuilder('a')
            ->where('a.date_debut > :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult();

        return [
            'total' => $total,
            'current' => count($current),
            'upcoming' => count($upcoming),
        ];
    }
}