<?php

namespace App\Form\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Section;
use App\Entity\Development\Tag;
use App\Extensions\FormExtension\CustomSelectEntityType;
use App\Extensions\FormExtension\SearchableEntityType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class DevelopmentAddType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label'    => false,
                'attr'     => [
                    'placeholder' => 'Enter a title'
                ]
            ])
            ->add('content', CKEditorType::class, [
                    'config' => [
                        'uiColor' => '#ffffff'
                    ],
                    'label'  => false
                ]
            )
            ->add('files', CollectionType::class, [
                'mapped'         => false,
                'label'          => 'Files',
                'entry_type'     => DevelopmentFileType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'error_bubbling' => false
            ])
            ->add('section', EntityType::class, [
                'label'       => false,
                'placeholder' => 'Select a section',
                'class'       => Section::class
            ])
            ->add('notes', CollectionType::class, [
                'label'          => 'Notes',
                'entry_type'     => NoteType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'disabled'       => false,
                'error_bubbling' => false
            ])
            ->add('posts', CollectionType::class, [
                'label'          => 'Posts',
                'entry_type'     => PostType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'disabled'       => false,
                'error_bubbling' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $dev = $event->getForm()->getViewData();
                if (count($dev->getNotes()) > 0) {
                    foreach ($dev->getNotes() as $note) {
                        $note->setUser($this->security->getUser());
                    }
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                if (count($event->getData()->getPosts()) > 0) {
                    $event->getForm()->add('author', TextType::class, [
                        'label'    => 'user',
                        'required' => false,
                        'mapped'   => false
                    ]);
                }
                if (count($event->getData()->getTags()) > 0) {
                    $event->getForm()->add('tags', SearchableEntityType::class, [
                        'class'          => Tag::class,
                        'search'         => $this->router->generate('api_tag_development'),
                        'label_property' => 'name'
                    ]);
                } else {
                    $event->getForm()->add('tags', CustomSelectEntityType::class, [
                        'class' => Tag::class,
                        'label' => false,
                        'attr'=>[
                            'placeholder'=>'Select tag(s)'
                        ]
                    ]);
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Development::class,
        ]);
    }
}
