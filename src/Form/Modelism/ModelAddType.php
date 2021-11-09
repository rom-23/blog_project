<?php

namespace App\Form\Modelism;

use App\Entity\Modelism\Category;
use App\Entity\Modelism\Model;
use App\Entity\Modelism\Option;
use App\Extensions\FormExtension\CustomSelectEntityType;
use App\Extensions\FormExtension\SearchableEntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Image;

class ModelAddType extends AbstractType
{
    private UrlGeneratorInterface $router;
    private Security $security;

    public function __construct(Security $security, UrlGeneratorInterface $router)
    {
        $this->router   = $router;
        $this->security = $security;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Enter a name'
                ],
                'label'    => false
            ])
            ->add('description', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff'
                ],
                'label'  => false
            ])
            ->add('price', MoneyType::class,[
                'attr'     => [
                    'placeholder' => 'Enter a price'
                ],
            ])
            ->add('thumbnail', FileType::class, [
                'label'    => 'Thumbnail',
                'mapped'   => false,
                'required' => false,
                'attr'=>[
                    'class'=>'form-control form-control-sm'
                ]
            ])
            ->add('images', CollectionType::class, [
                'mapped'         => false,
                'label'          => 'Create a gallery',
                'entry_type'     => ImageType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'error_bubbling' => false
            ])
            ->add('opinions', CollectionType::class, [
                'label'          => 'Opinions',
                'entry_type'     => OpinionType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'disabled'       => false,
                'error_bubbling' => false
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                if (count($event->getData()->getCategories()) > 0) {
                    $event->getForm()->add('categories', SearchableEntityType::class, [
                        'class'          => Category::class,
                        'search'         => $this->router->generate('api_category_model'),
                        'label_property' => 'name'
                    ]);
                } else {
                    $event->getForm()->add('categories', CustomSelectEntityType::class, [
                        'class' => Category::class,
                        'label' => false,
                        'attr'=>[
                            'placeholder'=>'Select categories'
                        ]
                    ]);
                }
                if (count($event->getData()->getOptions()) > 0) {
                    $event->getForm()->add('options', SearchableEntityType::class, [
                        'class'          => Option::class,
                        'search'         => $this->router->generate('api_option_model'),
                        'label_property' => 'name'
                    ]);
                } else {
                    $event->getForm()->add('options', CustomSelectEntityType::class, [
                        'class' => Option::class,
                        'label' => false,
                        'attr'=>[
                            'placeholder'=>'Select option(s)'
                        ]
                    ]);
                }
            })
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
        ]);
    }
}
