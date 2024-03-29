<?php

namespace App\Form;

use App\Entity\enduser;
use App\Entity\messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MessagerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('senderId_message', EntityType::class,[
            'class'=>enduser::class,
        'choice_label'=>'username'])
        ->add('receiverId_message', EntityType::class,[
            'class'=>enduser::class,
        'choice_label'=>'username'])
        ->add('contenu_message')
        ->add('type_message')
        ->add('date_message', DateType::class, [
                'label' => 'Date du message',
                'widget' => 'single_text', // Utiliser un widget simple pour la sélection de date
                'html5' => false, // Désactiver le support HTML5 pour une compatibilité maximale
                'format' => 'yyyy-MM-dd' // Format de la date
            ])
        ->add('envoyer', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => messagerie::class,

        ]);
    }
}
