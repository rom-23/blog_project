<?php

namespace App\Validator\User;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class UniqueEmail extends Constraint
{
    public string $message = 'This email already exists';

}
