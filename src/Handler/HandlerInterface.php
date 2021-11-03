<?php

namespace App\Handler;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    /**
     * @param Request $request
     * @param object $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, object $data, array $options=[]): bool;

    public function createView(): FormView;
}
