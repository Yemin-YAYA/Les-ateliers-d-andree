<?php

// src/Form/ContactType.php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
            ])
            ->add('raison', ChoiceType::class, [
                'choices' => [
                    'Questions' => 'Questions',
                    'Demande de devis' => 'Demande de devis',
                    "Droit d'accès aux données" => "Droit d'accès aux données",
                    
                ],
                'placeholder' => 'Motif du contact', 
                'expanded' => false, 
                'multiple' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'csrf_protection' => true, // CSRF est activé par défaut
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'contact_item', // Identifiant unique pour le CSRF
        ]);
    }
}
