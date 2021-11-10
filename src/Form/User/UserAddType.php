<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\Development\NoteType;
use App\Form\Development\PostType;
use App\Form\Modelism\OpinionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class UserAddType extends AbstractType
{

    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'    => 'Email',
                'required' => true,
                'attr'     => [
                    'autofocus'   => true,
                    'placeholder' => 'User email',
                    'class'       => 'form-control'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'User'  => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label'    => 'Roles',
                'attr'     => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('password', TextType::class, [
                'label'    => 'Password',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'User password',
                    'class'       => 'form-control'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label'       => false,
                'mapped'      => false,
                'required'    => false,
                'attr'        => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new Image([
                        'maxSize'   => '2M',
                        'mimeTypes' => [
                            'image/*'
                        ]
                    ]),
                    new NotNull([
                        'groups' => 'create'
                    ])
                ]
            ])
            ->add('isVerified', CheckboxType::class, [
                'label'    => 'Is verified',
                'required' => true,
                'attr'     => [
                    'class' => 'form-check-input'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr'  => [
                    'class' => 'btn btn-warning btn-sm'
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                if (strpos($this->router->getContext()->getPathInfo(), 'root') == true) {
                    $event->getForm()->add('posts', CollectionType::class, [
                        'label'          => 'Posts',
                        'entry_type'     => PostType::class,
                        'prototype'      => true,
                        'allow_add'      => true,
                        'allow_delete'   => true,
                        'by_reference'   => false,
                        'required'       => false,
                        'disabled'       => false,
                        'error_bubbling' => false
                    ]);
                    $event->getForm()->add('notes', CollectionType::class, [
                        'label'          => 'Notes',
                        'entry_type'     => NoteType::class,
                        'prototype'      => true,
                        'allow_add'      => true,
                        'allow_delete'   => true,
                        'by_reference'   => false,
                        'required'       => false,
                        'disabled'       => false,
                        'error_bubbling' => false
                    ]);
                    $event->getForm()->add('opinions', CollectionType::class, [
                        'label'          => 'Opinions',
                        'entry_type'     => OpinionType::class,
                        'prototype'      => true,
                        'allow_add'      => true,
                        'allow_delete'   => true,
                        'by_reference'   => false,
                        'required'       => false,
                        'disabled'       => false,
                        'error_bubbling' => false
                    ]);
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
