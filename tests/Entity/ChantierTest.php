<?php

namespace App\Tests\Entity;

use App\Entity\Chantier;
use App\Entity\Ouvrier;
use PHPUnit\Framework\TestCase;

class ChantierTest extends TestCase
{
    public function testChantierProperties()
    {
        $chantier = new Chantier();
        $ouvrier = new Ouvrier();
        $chantierPrerequis = ['Autorisation', 'Matériel'];
        $dateDebut = new \DateTime('2025-03-06');
        $dateFin = new \DateTime('2025-06-06');

        $chantier->setNom('Chantier Test');
        $chantier->setChantierPrerequis($chantierPrerequis);
        $chantier->setEffectifRequis(10);
        $chantier->setDateDebut($dateDebut);
        $chantier->setDateFin($dateFin);
        $chantier->setChefChantier($ouvrier);
        $chantier->setImage('image.jpg');

        $this->assertSame('Chantier Test', $chantier->getNom());
        $this->assertSame($chantierPrerequis, $chantier->getChantierPrerequis());
        $this->assertSame(10, $chantier->getEffectifRequis());
        $this->assertSame($dateDebut, $chantier->getDateDebut());
        $this->assertSame($dateFin, $chantier->getDateFin());
        $this->assertSame($ouvrier, $chantier->getChefChantier());
        $this->assertSame('image.jpg', $chantier->getImage());
    }
}