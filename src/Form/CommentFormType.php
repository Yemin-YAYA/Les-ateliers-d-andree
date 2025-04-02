<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      
        $builder
            
            ->add('content', TextareaType::class, [
                    'attr' => [
                        'placeholder' => 'Écrivez votre commentaire ici...', // Placeholder du champ
                        'rows' => 5, // Nombre de lignes du textarea
                        'style' => 'width: 100%;' // Assure que le textarea occupe toute la largeur de la colonne
                    ],
                    'label' => false // Masquer le label si souhaité
                ])
            ->add('save', SubmitType::class, [
                    'label' => 'Envoyer',
                    'attr' => [
                        'class' => 'btn btn-sm btn-success mt-3' // Styliser le bouton de soumission
                    ]
                ])   
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
