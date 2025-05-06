<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'First Name', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'First name is required']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'First name must be at least {{ limit }} characters',
                        'max' => 50
                    ])
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Last Name', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Last name is required']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Last name must be at least {{ limit }} characters',
                        'max' => 50
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Email Address', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Email is required']),
                    new Email(['message' => 'Please enter a valid email address'])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords must match',
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Password',
                        'class' => 'form-control',
                        'data-requirements' => 'true' // Ajout d'un attribut data
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Password is required']),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Password must be at least {{ limit }} characters'
                        ]),
                        new Regex([
                            'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
                            'message' => 'Password requirements:uppercase letter, one lowercase letter, one number...'
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Confirm Password', 'class' => 'form-control']
                ],
                'mapped' => false
            ])
            ->add('phone_num', TelType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Phone Number', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Phone number is required']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Phone number must be at least {{ limit }} digits',
                        'max' => 15
                    ])
                ]
            ])
            ->add('address', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Tunisia' => 'tunisia',
                    'Egypt' => 'egypt',
                    'Algerie' => 'algerie',
                    'Other' => 'other'
                ],
                'placeholder' => 'Select Address',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Address is required'])
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}