<?php

namespace App\Form;

use App\Entity\tache;
use App\Enum\EtatTache;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                'label' => 'Nom Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ])
            ->add('titre_T', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('pieceJointe_T', TextType::class, [
                'label' => 'Pièce Jointe',
            ])
            ->add('date_DT', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Début',
            ])
            ->add('date_FT', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Fin',
            ])
            ->add('desc_T', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('etat_T', ChoiceType::class, [
                'choices' => EtatTache::toArray(),
                'expanded' => true,
                'multiple' => false,
                'label' => 'Etat',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => tache::class,
        ]);
    }
}
