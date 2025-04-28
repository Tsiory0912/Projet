<?php

namespace App\Form;

use App\Entity\Agent;
use App\Entity\Article;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;  // Assurez-vous de charger DateType pour les dates

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_sortie', DateType::class, [  // Utilisation de DateType pour les dates
                'widget' => 'single_text',  // Affichage comme un champ de texte unique pour la date
                'format' => 'yyyy-MM-dd',  // Format de la date, facultatif mais recommandé
            ])
            ->add('quantite')  // Aucun problème avec TextType ici
            ->add('observation')  // Aucun problème avec TextType ici
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'nom',
            ])
            ->add('agent', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
