<?php
namespace App\Form;


use App\Entity\reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReclamationModifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('sujet_reclamation')
            ->add('type_reclamation', ChoiceType::class, [
                'label' => 'Type de la réclamation',
                'placeholder' => 'Type de la réclamation',
                'choices' => [
                    'Urgences médicales' => 'Urgences médicales',
                'Incendies' => 'Incendies',
                'Fuites de gaz' => 'Fuites de gaz',
                'Inondations' => 'Inondations',
                'Défaillances des infrastructures critiques' => 'Défaillances des infrastructures critiques',
                'Réparations de voirie' => 'Réparations de voirie',
                'Collecte des déchets' => 'Collecte des déchets',
                'Environnement' => 'Environnement',
                'Aménagement paysager' => 'Aménagement paysager',
                'Problèmes de logement' => 'Problèmes de logement',
                'Services municipaux' => 'Services municipaux',
                ],
            ])

            ->add('description_reclamation', TextareaType::class, [ // Utilisation de TextareaType au lieu de la valeur par défaut
                'label' => 'Description de la réclamation',
                'attr' => ['class' => 'form-control', 'style' => 'height: 107px;'], // Ajout de classes et de styles
            ])
            ->add('image_reclamation', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => false, // It's not required
            ])
            ->add('adresse_reclamation')
            ->add('modifier', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => reclamation::class,
        ]);
    }
}
