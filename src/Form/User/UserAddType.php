<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class UserAddType extends AbstractType
{
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
            ]);
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
