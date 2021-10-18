<?php

namespace App\Form\User;

use App\Entity\User;
use App\Extensions\FormExtension\RepeatPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'    => false,
                'required' => true,
                'attr'     => [
                    'autofocus'   => true,
                    'placeholder' => 'Your email'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'       => 'Accept CGU',
                'required'    => true,
                'mapped'      => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatPasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
