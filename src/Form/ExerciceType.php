<?php

namespace App\Form;

use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExerciceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('content',TextareaType::class, 
            [    'attr'=>['class'=>'form-control']
            ])
            ->add('description',TextType::class, [    'attr'=>['class'=>'form-control']
            ])
            ->add('picture',FileType::class,['label'=>false,
            'multiple'=>true,
            'mapped'=>false,
            'required'=>false,
            'attr'=>['class'=>'form-control', 'required'=>'false',]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}
