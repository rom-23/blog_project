<?php

namespace App\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var FormInterface
     */
    protected FormInterface $form;

    abstract protected function getFormType(): string;

    abstract protected function process(object $data, Request $request): void;

    /**
     * @required
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @param object $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, object $data, array $options = []): bool
    {
        $this->form = $this->formFactory->create($this->getFormType(), $data, $options)->handleRequest($request);
        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data, $request);
            return true;
        }
        return false;
    }

    public function createView(): FormView
    {
        return $this->form->createView();
    }


}
