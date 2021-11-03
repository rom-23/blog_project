<?php

namespace App\Validator\User;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $existingUser = $this->userRepository->findOneBy([
            'email' => $value
        ]);
        if (!$existingUser) {
            return;
        }
        /* @var $constraint UniqueEmail */
        $this->context->buildViolation($constraint->message)->addViolation();
    }

}
