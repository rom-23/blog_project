<?php

namespace App\Form\Modelism;

use App\Entity\Modelism\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as img;

class ImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FileType::class, [
                'required'    => false,
                'label'       => false,
                'attr'        => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new img([
                        'maxSize'   => '2M',
                        'mimeTypes' => [
                            'image/*'
                        ]
                    ])
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
