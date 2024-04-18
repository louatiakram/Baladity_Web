<?php

namespace App\Form;

use App\Entity\enduser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // You may want to remove the 'id_user' field if it's auto-generated
            // ->add('id_user') 

            ->add('nom_user', TextType::class, [
                'label' => 'Username', // Customize the label
                // Add more options if needed (e.g., required, constraints)
            ])
            ->add('email_user', TextType::class, [
                'label' => 'Email', // Customize the label
                // Add more options if needed (e.g., required, constraints)
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password', // Customize the label
                // Add more options if needed (e.g., required, constraints)
            ])
            ->add('type_user', ChoiceType::class, [
                'label' => 'User Type',
                'required' => false,
                'choices' => [
                    'Citoyen' => 'Citoyen',
                    'Directeur' => 'Directeur',
                    'Employé' => 'Employé',
                    'Responsable employé' => 'Responsable employé',
                ],
            ])
            ->add('phoneNumber_user', TextType::class, [    
                'label' => 'Phone Number', // Customize the label
                // Add more options if needed (e.g., required, constraints)
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Phone number must be exactly {{ limit }} digits long.',
                    ]),
                ],
            ])
            // Assuming 'id_muni' is a ManyToOne relationship, consider using EntityType instead of TextType
            // ->add('id_muni') 
            ->add('id_muni', EntityType::class, [
                'class' => 'App\Entity\muni',
                'choice_label' => 'nom_muni', // Assuming you want to display the municipality name
                'label' => 'Municipality', // Customize the label
                // Add more options if needed
            ])

            ->add('location_user', TextType::class, [
                'label' => 'Location', // Customize the label
                // Add more options if needed (e.g., required, constraints)
            ])
            ->add('image_user', FileType::class, [ // Add FileType for image upload
                'label' => 'Image URL',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => true, // It's required
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enduser::class,
        ]);
    }
}
