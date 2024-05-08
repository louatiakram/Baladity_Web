<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Import FileType
use App\Entity\actualite;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ActualiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre_a', TextType::class, [
                'attr' => [
                    'placeholder' => 'Votre titre ici',
                ],
            ])
            ->add('description_a', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Votre description ici',
                ],
            ])
            ->add('image_a', FileType::class, [ // Add FileType for image upload
                'label' => 'Image',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => false, // It's not required
            ])
          ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => actualite::class,
        ]);
    }
}
