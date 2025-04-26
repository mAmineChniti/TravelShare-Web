<?php

namespace App\Form;

use App\Entity\Users;
use App\Service\CountryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    private CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'First Name'],
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
                'attr' => ['placeholder' => 'Last Name'],
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
                'attr' => ['placeholder' => 'Email Address'],
                'constraints' => [
                    new NotBlank(['message' => 'Email address is required']),
                    new Email(['message' => 'Please enter a valid email address'])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Password',
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Password is required']),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => 'Your password must contain at least one uppercase, one lowercase, one number and one special character'
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Repeat Password',
                        'class' => 'form-control'
                    ]
                ],
            ])
            ->add('phone_num', TelType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Phone Number'],
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
                'choices' => $this->countryService->getCountries(),
                'placeholder' => 'Select your country',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
