<?php

namespace App\Tests\Entity;

use App\Entity\Equipe;
use App\Entity\Ouvrier;
use PHPUnit\Framework\TestCase;

class EquipeTest extends TestCase
{
    public function testEquipeProperties()
    {
        $equipe = new Equipe();
        $chef = new Ouvrier();
        $competences = ['Plomberie', 'Maçonnerie'];

        $equipe->setNomEquipe('Équipe Alpha');
        $equipe->setCompetanceEquipe($competences);
        $equipe->setNombre(5);
        $equipe->setChefEquipe($chef);

        $this->assertSame('Équipe Alpha', $equipe->getNomEquipe());
        $this->assertSame($competences, $equipe->getCompetanceEquipe());
        $this->assertSame(5, $equipe->getNombre());
        $this->assertSame($chef, $equipe->getChefEquipe());
    }
}