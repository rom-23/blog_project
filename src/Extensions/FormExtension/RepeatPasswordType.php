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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'           => false,
            'type'            => PasswordType::class,
            'invalid_message' => 'Password must be the same',
            'required'        => true,
            'first_options'   => [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Your password',
                    'class'=>'mb-3'
                ]
            ],
            'second_options'  => [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Confirm your password',
                    'class'=>'mb-3'
                ]
            ]
        ]);
    }
}
