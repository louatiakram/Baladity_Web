<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom')
            ->add('offre_pub', ChoiceType::class, [ // Utilize ChoiceType for dropdown
                'choices' => [ // Define choices
                    '3 mois :50dt ' => '3 mois :50dt',
                    '6 mois :90dt' => '6 mois :90dt',
                    '9 mois :130dt ' => '9 mois :130dt',
                   
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
