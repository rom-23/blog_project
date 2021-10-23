<?php

namespace App\Form\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Section;
use App\Entity\Development\Tag;
use App\Extensions\FormExtension\CustomSelectEntityType;
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
                'label' => false,
                'attr'  => [
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
                'mapped' => false,
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
            ->add('tags', CustomSelectEntityType::class, [
                'class' => Tag::class,
                'label' => false
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
