<?php

namespace App\Form;

use App\Entity\Muni;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Muni::class,
        ]);
    }
}
