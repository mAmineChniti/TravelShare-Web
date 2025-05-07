<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\Length;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_email', EmailType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email is required',
                    ]),
                    new EmailConstraint([
                        'message' => 'Please enter a valid email address',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Email Address',
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Password is required',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Password must be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Password',
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new RecaptchaTrue([
                        'message' => 'Please verify that you are not a robot',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        // Important pour éviter _email et _password d'avoir un prefix genre login_form[_email]
        return '';
    }
}
