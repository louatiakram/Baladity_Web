<?php

namespace App\Form;

use App\Entity\tache;
use App\Enum\EtatTache;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_Cat', ChoiceType::class, [
                'choices' => [
                    'Employé' => 'Employé',
                    'Responsable employé' => 'Responsable employé',
                ],
                'placeholder' => 'Categorie',
            ])
            ->add('titre_T', TextType::class)
            ->add('pieceJointe_T', FileType::class, [
                'mapped' => false,

            ])
            ->add('date_DT', DateType::class, [
                'widget' => 'single_text',

            ])
            ->add('date_FT', DateType::class, [
                'widget' => 'single_text',

            ])
            ->add('desc_T', TextareaType::class)
            ->add('etat_T', ChoiceType::class, [
                'choices' => EtatTache::toArray(),
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('Save', SubmitType::class)
            ->add('Reset', ResetType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => tache::class,
        ]);
    }
}