<?php

namespace App\Form;

use App\Entity\Enduser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdminEditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type_user', ChoiceType::class, [
            'label' => 'User Type',
            'required' => false,
            'choices' => [
                'Citoyen' => 'Citoyen',
                'Directeur' => 'Directeur',
                'Employé' => 'Employé',
                'Responsable employé' => 'Responsable employé',
                'Admin' => 'Admin',
            ],
            'data' => $options['data']->getTypeUser(), // Set 'Citoyen' as the default choice
        ])
        
            ->add('isBanned', ChoiceType::class, [
                'label' => 'isBanned', // Customize the label
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'expanded' => true, // This will display radio buttons for choices
                'multiple' => false, // Allow only one selection
                'required' => true, // Make it required, adjust as needed
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enduser::class,
        ]);
    }
}
