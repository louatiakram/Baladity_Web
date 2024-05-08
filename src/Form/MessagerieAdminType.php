<?php

namespace App\Form;

use App\Entity\enduser;
use App\Entity\messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MessagerieAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('senderId_message', EntityType::class,[
            'class'=>enduser::class,
            'choice_label' => function ($enduser) {
                // Si c'est un directeur, affiche "Directeur", sinon, affiche le nom de l'utilisateur
                return $enduser->getTypeUser() === 'directeur' ? 'Directeur' : $enduser->getNomUser();
            }])
        ->add('receiverId_message', EntityType::class,[
            'class'=>enduser::class,
      'choice_label' => function ($enduser) {
                    // Si c'est un directeur, affiche "Directeur", sinon, affiche le nom de l'utilisateur
                    return $enduser->getTypeUser() === 'directeur' ? 'Directeur' : $enduser->getNomUser();
                }])
        ->add('contenu_message')
        ->add('type_message', ChoiceType::class, [
                'label' => 'Type de message',
                'placeholder' => 'Type de message',
                'choices' => [
                    'vocal' => 'vocal',
                    'text' => 'text',
                ]
        ])
        ->add('date_message', HiddenType::class, [
            'mapped' => false, // Assurez-vous que le champ ne soit pas mappé à une propriété de l'entité
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
