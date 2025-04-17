<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_email', EmailType::class, [  // Changed from 'email' to '_email'
                'label' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Email is required']),
                    new Email(['message' => 'Please enter a valid email address']),
                ],
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'form-control',
                    'autocomplete' => 'email'  // Added for better UX
                ]
            ])
            ->add('_password', PasswordType::class, [  // Changed from 'password' to '_password'
                'label' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Password is required']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Password must be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 255,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Password',
                    'class' => 'form-control',
                    'autocomplete' => 'current-password'  // Added for better UX
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,  // Important for security
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',  // Matches security.yaml config
        ]);
    }

    // Optional: remove the form name prefix
    public function getBlockPrefix(): string
    {
        return '';  // This makes the field names exactly '_email' and '_password'
    }
}
