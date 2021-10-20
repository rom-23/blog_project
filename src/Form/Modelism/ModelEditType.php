<?php

namespace App\Form\Modelism;

use App\Entity\Modelism\Category;
use App\Entity\Modelism\Model;
use App\Entity\Modelism\Option;
use App\Extensions\FormExtension\CustomSelectEntityType;
use App\Repository\Modelism\CategoryRepository;
use App\Repository\Modelism\OptionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelEditType extends AbstractType
{
    private CategoryRepository $categoryRepository;
    private OptionRepository $optionRepository;

    public function __construct(CategoryRepository $categoryRepository, OptionRepository $optionRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->optionRepository   = $optionRepository;
    }

    /** buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'Thumb'
            ])
            ->add('originalFile', FileType::class, [
                'label' => 'Originale'
            ])
            ->add('name', TextType::class, [
                'required'   => true,
                'label_attr' => [
                    'required' => 'required'
                ],
                'label'      => 'Name',
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
            ])
            ->add('price', MoneyType::class)
            ->add('options', CustomSelectEntityType::class, [
                'class' => Option::class,
                'attr'  => [
                    'placeholder' => 'Select option(s)'
                ],
                'label' => false
            ])
            ->add('images', CollectionType::class, [
                'entry_type'   => ImageType::class,
                'prototype'    => true,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'     => false,
                'label'        => false,
                'disabled'     => false
            ])
            ->add('categories', CustomSelectEntityType::class, [
                'class' => Category::class,
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Select categories'
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
        ]);
    }
}
