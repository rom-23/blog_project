<?php

namespace App\Form\Development;

use App\Entity\Development\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr'  => [
                    'class' => 'form-control',
                    'placeholder'=>'Title'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Comment',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('development')
//            ->add('user')
            ->add('parentid', HiddenType::class, [
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send post',
                'attr'  => [
                    'class' => 'btn btn-info mt-2 text-white pt-1 pb-1'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
