<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference_eq', TextType::class)
            ->add('nom_eq', TextType::class)
            ->add('categorie_eq', ChoiceType::class, [
                'choices' => [
                    'Mobile' => 'Mobile',
                    'Fixe' => 'Fixe',
                ],
                'placeholder' => 'Categorie',
            ])
            ->add('quantite_eq', IntegerType::class)
            ->add('date_ajouteq', DateType::class)
            ->add('description_eq', TextareaType::class)
            ->add('image_eq', FileType::class, [ // Add FileType for image upload
                'label' => 'Image',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => false, // It's not required
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}
