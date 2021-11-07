<?php

namespace App\Form\Development;

use App\Entity\Development\Note;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class NoteType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>false,
                'required'=>true,
                'attr'=>[
                    'placeholder'=>'Title'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label'=>false,
                'required'=>true,
                'attr'=>[
                    'placeholder'=>'Enter your note ...'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
