<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'    => false,
                'disabled' => true,
                'attr'=>[
                    'class'=>'mb-3'
                ]
            ])
            ->add('old_password', PasswordType::class, [
                'required' => true,
                'label'    => false,
                'mapped'   => false,
                'attr'     => [
                    'placeholder' => 'Actual password',
                    'class'=>'mb-5'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'invalid_message' => 'Both passwords must be the same.',
                'required'        => true,
                'label'           => false,
                'first_options'   => [
                    'label' => false,
                    'attr'  => [
                        'placeholder' => 'Type your new password',
                        'class'=>'mb-2'
                    ]
                ],
                'second_options'  => [
                    'label' => false,
                    'attr'  => [
                        'placeholder' => 'Confirm your new password'
                    ]
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
