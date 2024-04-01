<?php

namespace App\Form;

use App\Entity\enduser;
use App\Entity\messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MessagerieModificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu_message')
            ->add('type_message')
            ->add('date_message', HiddenType::class, [
                'mapped' => false, // Assurez-vous que le champ n'est pas mappé à une propriété de l'entité
            ])
            ->add('modifier', SubmitType::class);
        
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => messagerie::class,
        ]);
    }
}
