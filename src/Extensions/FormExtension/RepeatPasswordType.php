<?php

namespace App\Extensions\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatPasswordType extends AbstractType
{
    public function getParent(): string
    {
        return RepeatedType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label'           => 'Password',
            'type'            => PasswordType::class,
            'invalid_message' => 'Password must be the same',
            'required'        => true,
            'first_options'   => [
                'label' => 'password',
                'attr'  => [
                    'placeholder' => 'Your password'
                ]
            ],
            'second_options'  => [
                'label' => 'confirm your password',
                'attr'  => [
                    'placeholder' => 'Your password'
                ]
            ]
        ]);
    }
}
