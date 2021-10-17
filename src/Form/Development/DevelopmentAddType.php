<?php

namespace App\Form\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Section;
use App\Entity\Development\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class DevelopmentAddType extends AbstractType
{
    private $token;
    private $router;

    public function __construct(TokenStorageInterface $token,UrlGeneratorInterface $router)
    {
        $this->token = $token;
        $this->router = $router;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->add('file', FileType::class, [
                'required'    => false,
                'label'       => false,
                'constraints' => [
                    new File([
                        'maxSize'          => '5M',
                        'mimeTypes'        => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ]
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
                dd($event->getForm()->getConfig()->getOptions());
                if (count($dev->getNotes()) > 0) {
                    if ($this->token->getToken() === null) {

                        $url = $this->router->generate('app_login');

                        return new RedirectResponse($url);

                    }else{
//                        dd($this->token->getToken()->getUser(),$dev->getPosts());
                        $user =$this->token->getToken()->getUser();
//                        dd($dev->getPosts());
                        foreach ($dev->getNotes() as $note) {
                            $user->addNote($note);
                        }
                        dd($user);

                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Development::class,
        ]);
    }
}
