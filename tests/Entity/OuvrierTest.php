<?php

namespace App\Tests\Entity;

use App\Entity\Ouvrier;
use App\Entity\Equipe;
use PHPUnit\Framework\TestCase;

class OuvrierTest extends TestCase
{
    public function testOuvrierProperties()
    {
        $ouvrier = new Ouvrier();
        $equipe = new Equipe();
        $competences = ['Électricité', 'Soudure'];

        $ouvrier->setNomOuvrier('Jean Dupont');
        $ouvrier->setCompetences($competences);
        $ouvrier->setRole('Électricien');
        $ouvrier->setEquipe($equipe);

        $this->assertSame('Jean Dupont', $ouvrier->getNomOuvrier());
        $this->assertSame($competences, $ouvrier->getCompetences());
        $this->assertSame('Électricien', $ouvrier->getRole());
        $this->assertSame($equipe, $ouvrier->getEquipe());
    }
}