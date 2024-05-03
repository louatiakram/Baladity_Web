<?php

namespace App\Form;

use App\Entity\enduser;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_user', TextType::class, [
            'label' => 'Username', // Customize the label
            // Add more options if needed (e.g., required, constraints)
        ])
        ->add('email_user', TextType::class, [
            'label' => 'Email', // Customize the label
            // Add more options if needed (e.g., required, constraints)
        ])
        
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
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
        ])
        ->add('captcha', Recaptcha3Type::class, [
            'constraints' => new Recaptcha3(),
            'action_name' => 'app_register',
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => enduser::class,
        ]);
    }
}
