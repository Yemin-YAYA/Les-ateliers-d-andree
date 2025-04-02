<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Le champ 'password' sans label, avec les attributs CSS et placeholder
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,  // Ce champ ne sera pas mappé sur une propriété de l'entité
                'label' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre nouveau mot de passe',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Merci d'entrer votre mot de passe",
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit contenir au moins {{ limit }} caractères.",
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&])[A-Za-z\d@$!%?&]{8,}$/',
                        'message' => 'Votre mot de passe doit comporter au moins une majuscule, un chiffre et un caractère spécial @$!%?&.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
