<?php

namespace App\Form\Development;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchableEntityType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
        $resolver->setDefaults([
            'required'       => false,
            'compound'       => false,
            'multiple'       => true,
            'search'         => '/search',
            'value_property' => 'id',
            'label_property' => 'name',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['expanded']                  = false;
        $view->vars['label']                     = 'Tags';
        $view->vars['attr']['class']             = 'advanced-select';
        $view->vars['placeholder']               = null;
        $view->vars['placeholder_in_choices']    = false;
        $view->vars['multiple']                  = true;
        $view->vars['preferred_choices']         = [];
        $view->vars['choices']                   = $this->choices($form->getData());
        $view->vars['choice_translation_domain'] = false;
        $view->vars['full_name']                 .= '[]';
        $view->vars['attr']['data-remote']       = $options['search'];
        $view->vars['attr']['data-value']        = $options['value_property'];
        $view->vars['attr']['data-label']        = $options['label_property'];
    }

    public function getBlockPrefix(): string
    {
        return 'choice';
    }

    private function choices(Collection $value): array
    {
        return $value
            ->map(fn($d) => new ChoiceView($d, (string)$d->getId(), (string)$d))
            ->toArray();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function (Collection $value): array {
                return $value->map(fn($d) => (string)$d->getId())->toArray();
            },
            function (?array $ids) use ($options): Collection {
                if (empty($ids)) {
                    return new ArrayCollection([]);
                }
                return new ArrayCollection(
                    $this->em->getRepository($options['class'])->findBy(['id' => $ids])
                );
            }
        ));
    }
}
