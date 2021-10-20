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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Title'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'Enter your note ...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
