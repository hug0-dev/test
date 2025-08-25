<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_equipe')
            ->add('competance_equipe', ChoiceType::class, [
                'choices' => [
                    'Maçon' => 'Maçon',
                    'Coffreur' => 'Coffreur',
                    'Ferrailleur' => 'Ferrailleur',
                    'Terrassier' => 'Terrassier',
                    "Conducteur d'engins" => "Conducteur d'engins",
                    'Manœuvre de chantier' => 'Manœuvre de chantier',
                    'Grutier' => 'Grutier',
                    'Plombier' => 'Plombier',
                    'Électricien' => 'Électricien',
                    'Peintre en bâtiment' => 'Peintre en bâtiment',
                    'Plâtrier' => 'Plâtrier',
                    'Carreleur' => 'Carreleur',
                    'Menuisier' => 'Menuisier',
                    'Parqueteur' => 'Parqueteur',
                    'Serrurier-métallier' => 'Serrurier-métallier',
                    'Chauffagiste' => 'Chauffagiste',
                    'Enduiseur' => 'Enduiseur',
                    'Vitrificateur' => 'Vitrificateur',
                    'Solier-moquettiste' => 'Solier-moquettiste',
                    'Staffeur-Ornemaniste' => 'Staffeur-Ornemaniste',
                ],
                'multiple' => true,
                'expanded' => true,
                'placeholder' => 'Sélectionnez les compétences prérequises',
                'required' => true,
                'attr' => ['class' => 'form-check']
                ])
            ->add('ouvriers', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom_ouvrier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role_ouvrier = :role')
                        ->setParameter('role', 'Ouvrier');  
                },  
                'multiple' => true,               
                'expanded' => true,               
            ])
            ->add('chef_equipe', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom_ouvrier',  
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role_ouvrier = :role')
                        ->setParameter('role', 'Chef');
                },
                'placeholder' => 'Sélectionnez un chef d\'équipe',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}