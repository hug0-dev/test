<?php

namespace App\Form;

use App\Entity\Affectation;
use App\Entity\Chantier;
use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom affectation',
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de Début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('chantier', EntityType::class, [
                'class' => Chantier::class,
                'choice_label' => function (Chantier $chantier) {
                    return $chantier->getNom() . ' - Nombre effectif ' . $chantier->getEffectifRequis();
                },
                'attr' => ['class' => 'form-control'],
                'disabled' => true,
            ])
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => function(Equipe $equipe) {
                    // On affiche le nom, les compétences (joinées) et le nombre d'ouvriers
                    return $equipe->getNomEquipe() . ' - Compétences : ' . implode(', ', $equipe->getCompetanceEquipe()) . ' - Nombre : ' . $equipe->getNombre();
                },
                'multiple' => false,
                'expanded' => true,
                'constraints' => [
                    new Callback([$this, 'validateEffectif'])
                ]
            ]);
    }

    public function validateEffectif($value, ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $chantier = $form->get('chantier')->getData();
        
        if ($chantier && $value) {
            $effectifRequis = $chantier->getEffectifRequis();
            $effectifEquipe = $value->getNombre();
            
            if ($effectifEquipe != $effectifRequis) {
                $context->buildViolation('Le nombre d\'effectif de l\'équipe doit être égal au nombre d\'effectif requis du chantier.')
                    ->atPath('equipe')
                    ->addViolation();
            }
            
            
            $competencesRequises = $chantier->getChantierPrerequis(); 
            $competencesEquipe = $value->getCompetanceEquipe(); 

            if (empty($competencesEquipe) || empty(array_intersect($competencesRequises, $competencesEquipe))) {
                $context->buildViolation('L\'équipe doit posséder au moins une des compétences requises du chantier.')
                    ->atPath('equipe')
                    ->addViolation();
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Affectation::class,
        ]);
    }
}
