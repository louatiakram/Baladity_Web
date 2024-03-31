<?php

namespace App\Form;

use App\Entity\enduser;
use App\Entity\messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MessagerieModificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu_message')
            ->add('type_message')
            ->add('date_message', DateTimeType::class, [ // Utilisez DateTimeType au lieu de DateType
                'label' => 'Date du message',
                'widget' => 'single_text', // Utiliser un widget simple pour la sélection de date
                'html5' => false, // Désactiver le support HTML5 pour une compatibilité maximale
                'format' => 'yyyy-MM-dd HH:mm:ss' // Format de la date et de l'heure
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
