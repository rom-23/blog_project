<?php

namespace App\Validator\Development;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class UniqueTitle extends Constraint
{
    public string $message = 'This title already exists';

}
