<?php

namespace App\Form;

use App\Entity\enduser;
use App\Entity\reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class ReclamationAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id_user', EntityType::class, [
            'class' => EndUser::class,
            'choice_label' => 'nomUser',
            'placeholder' => 'Choisir un utilisateur',
            'required' => true,
        ])
        ->add('sujet_reclamation')
        ->add('type_reclamation', ChoiceType::class, [
            'label' => 'Type de la réclamation',
            'placeholder' => 'Type de la réclamation',
            'choices' => $options['type_reclamation_choices'], // Utilisation de l'option dynamique
            'attr' => [
                'class' => 'form-select'
            ]
        ])
        ->add('description_reclamation', TextareaType::class, [ // Utilisation de TextareaType au lieu de la valeur par défaut
            'label' => 'Description de la réclamation',
            'attr' => ['class' => 'form-control', 'style' => 'height: 107px;'], // Ajout de classes et de styles
        ])
        ->add('image_reclamation', FileType::class, [
            'label' => 'Image (PNG, JPEG)',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Assert\File([
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger une image au format valide.',
                ]),
            ],
        ])
        ->add('adresse_reclamation')
        ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => reclamation::class,
            'type_reclamation_choices' => [], // Définition de l'option par défaut
        ]);
    }
}
