<?php

namespace App\Form\User;

use App\Entity\User;
use App\Extensions\FormExtension\RepeatPasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotNull;

class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('imageFile', FileType::class, [
                'label'    => false,
                'mapped'   => false,
                'required' => false,
                'constraints'=>[
                    new Image()
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
