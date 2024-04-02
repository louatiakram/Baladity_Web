<?php

namespace App\Form;

use App\Entity\evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;


class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_E')
            ->add('date_DHE')
            ->add('date_DHF')
            ->add('capacite_E')
            ->add('categorie_E')
            ->add('imageEvent', FileType::class, [
                'label' => 'Image de l\'Événement',
                'mapped' => false, // Not mapped to the entity property
                'required' => false, // Allow the field to be empty
                'attr' => ['accept' => 'image/*'], // Specify accepted file types
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '5M', // Maximum file size allowed (5MB in this example)
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
    
}
