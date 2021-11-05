<?php

namespace App\Form\Development;

use App\Entity\Development\Note;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr'=>[
                    'placeholder'=>'Title'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label'=>false,
                'required'=>true,
                'attr'=>[
                    'placeholder'=>'Enter your note ...'
                ]
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
