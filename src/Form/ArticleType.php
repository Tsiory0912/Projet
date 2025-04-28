<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Unite;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => "Code de l'article",
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => "Nom de l'article",
                'required' => true,
            ])
            ->add('stockAlerte', IntegerType::class, [
                'label' => 'Stock Alerte',
                'required' => true,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
            ])
            ->add('unite', EntityType::class, [
                'class' => Unite::class,
                'choice_label' => 'nom',
            ])
            // Ajout du champ stock (relation avec Stock)
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'quantite', // Affiche la propriété quantite (ajuste si nécessaire)
                'mapped' => false, // Ce champ ne sera pas mappé directement à l'entité Article
                'required' => false, // Si ce champ peut être nul ou optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
