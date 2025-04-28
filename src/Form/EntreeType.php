<?php

namespace App\Form;

use App\Entity\Entree;
use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'nom', // Affiche le nom de l'article
                'label' => 'Article',
                'placeholder' => 'Sélectionner un article',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité entrée',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1
                ],
            ])
            ->add('dateEntree', DateType::class, [
                'label' => 'Date de l\'entrée',
                'widget' => 'single_text',
                'required' => false, // Permettre au champ d’être vide
                'attr' => [
                    'class' => 'form-control',
                ],
                'data' => new \DateTime(), // 🌟 Ajout : valeur par défaut à aujourd'hui
            ])
            ->add('observation', TextareaType::class, [
                'label' => 'Observation (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Ajoutez une remarque si nécessaire...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entree::class,
        ]);
    }
}
