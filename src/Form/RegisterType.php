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
            ->add('image_user', TextType::class, [
                'label' => 'Image URL', // Customize the label
                // Add more options if needed (e.g., required, constraints)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enduser::class,
        ]);
    }
}
