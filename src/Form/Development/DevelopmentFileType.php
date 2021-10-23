<?php

namespace App\Form\Development;

use App\Entity\Development\DevelopmentFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DevelopmentFileType extends AbstractType
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
                'constraints' => [
                    new File([
                        'maxSize'          => '4M',
                        'mimeTypes'        => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid pdf file. '
                    ])
                ]
//                'data_class' => null,
//                'mapped'   => false
            ])
            ->add('path', TextType::class, [
                'required' => false,
                'label'    => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevelopmentFile::class,
        ]);
    }
}
