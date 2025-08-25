<?php

namespace App\Tests\Entity;

use App\Entity\Affectation;
use App\Entity\Chantier;
use App\Entity\Equipe;
use PHPUnit\Framework\TestCase;

class AffectationTest extends TestCase
{
    public function testAffectationProperties()
    {
        $affectation = new Affectation();
        $equipe = new Equipe();
        $chantier = new Chantier();
        $dateDebut = new \DateTime('2025-03-06');
        $dateFin = new \DateTime('2025-04-06');

        $affectation->setEquipe($equipe);
        $affectation->setChantier($chantier);
        $affectation->setDateDebut($dateDebut);
        $affectation->setDateFin($dateFin);
        $affectation->setNom('Test Affectation');

        $this->assertSame($equipe, $affectation->getEquipe());
        $this->assertSame($chantier, $affectation->getChantier());
        $this->assertSame($dateDebut, $affectation->getDateDebut());
        $this->assertSame($dateFin, $affectation->getDateFin());
        $this->assertSame('Test Affectation', $affectation->getNom());
    }
}