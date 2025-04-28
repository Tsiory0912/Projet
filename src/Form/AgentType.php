<?php

namespace App\Form;

use App\Entity\Agent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;  // Ajout du type IntegerType pour numeroAgent
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroAgent', IntegerType::class, [
                'label'    => 'Numéro d’agent',
                'disabled' => true,                // Lecture seule
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('matricule', null, [
                'label' => 'Matricule',
                'attr'  => ['class' => 'form-control'],
            ])
            ->add('nom', null, [
                'label' => 'Nom',
                'attr'  => ['class' => 'form-control'],
            ])
            // ✅ Gestion correcte du champ "roles" en tant qu'array
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'label' => 'Rôles',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
        ]);
    }
}
