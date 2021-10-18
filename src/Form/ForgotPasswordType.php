<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type'            => EmailType::class,
                'invalid_message' => 'email addresses must be the same',
                'required'        => true,
//                'constraints'     => [
//                    new NotBlank(),
//                    new Email()
//                ],
                'first_options'   => [
                    'label' => 'Your email',
                    'attr'  => [
                        'placeholder' => 'Type your email'
                    ]
                ],
                'second_options'  => [
                    'label' => 'Confirm your email',
                    'attr'  => [
                        'placeholder' => 'Confirm your email'
                    ]
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
