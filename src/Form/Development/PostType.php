<?php

namespace App\Form\Development;

use App\Entity\Development\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;


class PostType extends AbstractType
{
    private Security $user;

    /**
     * @param Security $user
     */
    public function __construct(Security $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'Title'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Comment',
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('parentid', HiddenType::class, [
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send post',
                'attr'  => [
                    'class' => 'btn btn-info mt-2 text-white pt-1 pb-1'
                ]
            ]);

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            if ($event->getData()->getUser() !== null) {
//                return;
//            }
//            $event->getForm()->add('author', TextType::class, [
//                'label'    => 'Pseudo',
//                'required' => false,
//                'mapped'   => false
//            ]);
//        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
