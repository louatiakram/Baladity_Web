<?php

namespace App\Form;

use App\Entity\publicite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Import FileType
use App\Entity\actualite;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class PubliciteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('titre_pub')
            ->add('description_pub')
            ->add('contact_pub')
            ->add('localisation_pub')
            ->add('image_pub', FileType::class, [ // Add FileType for image upload
                'label' => 'Image',
                'mapped' => false, // This means it won't be mapped to an entity property directly
                'required' => false, // It's not required
            ])
            ->add('offre_pub', ChoiceType::class, [ // Utilize ChoiceType for dropdown
                'choices' => [ // Define choices
                    '3 mois :50dt ' => '3 mois :50dt',
                    '6 mois :90dt' => '6 mois :90dt',
                    '9 mois :130dt ' => '9 mois :130dt',
                   
                ],
            ])
         
            ->add('actualite', EntityType::class, [
                'class' => actualite::class,
                'choice_label' => 'titre_a',
                'placeholder' => 'choisir une actualité',
                'required' => true,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publicite::class,
        ]);
    }
}
