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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'    => false,
                'disabled' => true
            ])
            ->add('old_password', PasswordType::class, [
                'required' => true,
                'label'    => false,
                'mapped'   => false,
                'attr'     => [
                    'placeholder' => 'Actual password'
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
                        'placeholder' => 'Type your new password'
                    ]
                ],
                'second_options'  => [
                    'label' => false,
                    'attr'  => [
                        'placeholder' => 'Confirm your password'
                    ]
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
