<?php

namespace App\Form;

use App\Entity\enduser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email_user', TextType::class, [
            'label' => 'Email', // Customize the label
            // Add more options if needed (e.g., required, constraints)
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Password', // Customize the label
            // Add more options if needed (e.g., required, constraints)
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => enduser::class,
        ]);
    }
}
