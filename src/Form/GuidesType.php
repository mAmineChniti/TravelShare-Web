<?php

namespace App\Form;  // Add this line

use App\Entity\Guides;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Service\LanguageApiService;


class GuidesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNum', TextType::class)
            ->add('language', ChoiceType::class, [
                'choices' => $this->languageService->getLanguages(),
                'placeholder' => 'Languages ​​spoken',
                'attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('experience', ChoiceType::class, [
                'choices' => [
                    '5 ans' => '5',
                    '10 ans' => '10',
                    '15 ans' => '15',
                    '20 ans' => '20',
                    '25 ans et plus' => '25+',
                ],
                'placeholder' => 'Years of experience',
                'attr' => ['class' => 'form-control mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guides::class,
        ]);
    }

    private $languageService;

public function __construct(LanguageApiService $languageService)
{
    $this->languageService = $languageService;
}

}
