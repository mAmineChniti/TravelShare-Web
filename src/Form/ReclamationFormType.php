<?php

namespace App\Form;

use App\Entity\Reclamations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReclamationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Complaint subject',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your complaint subject',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a subject for your complaint',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Subject must be at least {{ limit }} characters',
                        'max' => 100,
                        'maxMessage' => 'Subject cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Detailed description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Describe your complaint in detail...',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Description must be at least {{ limit }} characters',
                        'max' => 2000,
                    ]),
                ],
            ])
            ->add('dateReclamation', DateType::class, [
                'label' => 'Complaint date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'data' => new \DateTime(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select a date',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamations::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation',
            ],
            'allow_extra_fields' => true, // Allow extra fields like user to be set programmatically
        ]);
    }
}
