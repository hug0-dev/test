<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = $options['is_admin'];
        $isNewUser = !$options['data'] || !$options['data']->getId();

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('nom_ouvrier', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('competences', ChoiceType::class, [
                'label' => 'Compétences',
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
                'required' => true,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('role_ouvrier', ChoiceType::class, [
                'label' => 'Fonction',
                'choices' => [
                    'Ouvrier' => 'Ouvrier',
                    'Chef d\'équipe' => 'Chef',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-check-input'],
                'required' => true
            ]);

        // Mot de passe seulement pour les nouveaux utilisateurs ou si admin
        if ($isNewUser) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'help' => 'Minimum 6 caractères'
            ]);
        } elseif ($isAdmin) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'help' => 'Laisser vide pour garder le mot de passe actuel'
            ]);
        }

        // Seuls les admins peuvent définir d'autres admins
        if ($isAdmin) {
            $builder->add('is_admin', CheckboxType::class, [
                'label' => 'Administrateur',
                'required' => false,
                'mapped' => false,
                'data' => in_array('ROLE_ADMIN', $options['data']?->getRoles() ?? []),
                'attr' => ['class' => 'form-check-input'],
                'help' => 'Cocher pour donner les droits d\'administration'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_admin' => false,
        ]);
    }
}