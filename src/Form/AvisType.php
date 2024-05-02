<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3; // Ajout de l'import pour Recaptcha3
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type; // Ajout de l'import pour Recaptcha3Type

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('note_avis', HiddenType::class, [
            'data' => 0,
        ])
        ->add('commentaire_avis', TextareaType::class)
        ->add('captcha', Recaptcha3Type::class, [
            'constraints' => new Recaptcha3(),
            'action_name' => 'ajouterAvisFront',
        ]);

    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
            'equipement' => null,  // Option pour passer l'ID de l'équipement au formulaire
        ]);
    }
}
