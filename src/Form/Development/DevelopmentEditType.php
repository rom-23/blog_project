<?php

namespace App\Form\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Tag;
use App\Extensions\FormExtension\SearchableEntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class DevelopmentEditType extends AbstractType
{
    private Security $security;
    private UrlGeneratorInterface $router;

    public function __construct(Security $security,UrlGeneratorInterface $router)
    {
        $this->security = $security;
        $this->router   = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<int|string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', CKEditorType::class, [
                'config'   => [
                    'uiColor' => '#ffffff'
                ],
                'label'    => false,
                'required' => true
            ])
            ->add('files', CollectionType::class, [
                'mapped' => false,
                'label'          => false,
                'entry_type'     => DevelopmentFileType::class,
                'prototype'      => true,
                'allow_add'      => true,
                'allow_delete'   => true,
                'by_reference'   => false,
                'required'       => false,
                'error_bubbling' => false
            ])
            ->add('section')
            ->add('tags', SearchableEntityType::class, [
                'class'          => Tag::class,
                'search'         => $this->router->generate('api_tag_development'),
                'label_property' => 'name'
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
                'label' => 'Edit'
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $dev = $event->getForm()->getViewData();
                if(count($dev->getNotes()) > 0) {

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
