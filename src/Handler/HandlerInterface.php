<?php

namespace App\Handler;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    /**
     * @param Request $request
     * @param $data
     * @return bool
     */
    public function handle(Request $request, $data): bool;

    public function createView(): FormView;
}
