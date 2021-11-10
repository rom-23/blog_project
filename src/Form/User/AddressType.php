<?php

namespace App\Form\User;

use App\Entity\Address;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Enter a name for your address',
                    'class'=>'mb-2'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Type your firstname',
                    'class'=>'mb-2'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Type your lastname',
                    'class'=>'mb-2'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Your address ...',
                    'class'=>'mb-2'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' =>false,
                'attr'  => [
                    'placeholder' => 'Enter your postal code',
                    'class'=>'mb-2'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Enter your city',
                    'class'=>'mb-2'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
