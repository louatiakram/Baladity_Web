<?php

namespace App\Form;

use App\Entity\Muni;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutMuniFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_muni')
            ->add('email_muni')
            ->add('password_muni', PasswordType::class,)
            ->add('imagee_user', FileType::class, [ // Add FileType for image upload
                'label' => 'Image URL',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => false, // It's not required
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Muni::class,
        ]);
    }
}
