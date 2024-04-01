<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border',
                    'placeholder' => 'Enter Title ...'
                ],
                'label' => false,
            ])
            ->add('releaseYear', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border',
                    'placeholder' => 'Enter Release Year ...'
                ],
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border',
                    'placeholder' => 'Enter Description ...'
                ],
                'label' => false,
            ])
            ->add('imagePath', FileType::class, [
                'attr' => [
                    'class' => 'bg-transparent block border',
                ],
                'label' => false,
                'required' => false,
                'mapped' => false,
            ])
//            ->add('actors', EntityType::class, [
//                'class' => Actor::class,
//                'choice_label' => 'id',
//                'multiple' => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
