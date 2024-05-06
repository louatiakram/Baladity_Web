<?php

namespace App\Form;

use App\Entity\Enduser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_user', TextType::class, [
            'label' => 'Username', // Customize the label
        ])
        ->add('email_user', TextType::class, [
            'label' => 'Email', // Customize the label
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Password', // Customize the label
        ])
        ->add('phoneNumber_user', TextType::class, [    
            'label' => 'Phone Number', // Customize the label
            'required' => true,
            'constraints' => [
                new Length([
                    'min' => 8,
                    'max' => 8,
                    'exactMessage' => 'Phone number must be exactly {{ limit }} digits long.',
                ]),
            ],
        ])

        ->add('location_user', TextType::class, [
            'label' => 'Location', // Customize the label
            // Add more options if needed (e.g., required, constraints)
        ])
        ->add('image_user', FileType::class, [ // Add FileType for image upload
            'label' => 'Image URL',
            'mapped' => false, // This means it won't be mapped to an entity property directly
            'required' => false, // It's not required
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                ])
            ],
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enduser::class,
        ]);
    }
}
