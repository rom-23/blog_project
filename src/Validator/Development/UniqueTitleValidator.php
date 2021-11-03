<?php

namespace App\Validator\Development;

use App\Repository\Development\DevelopmentRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueTitleValidator extends ConstraintValidator
{

    /**
     * @var DevelopmentRepository
     */
    private DevelopmentRepository $developmentRepository;

    /**
     * @param DevelopmentRepository $developmentRepository
     */
    public function __construct(DevelopmentRepository $developmentRepository)
    {
        $this->developmentRepository = $developmentRepository;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $existingTitle = $this->developmentRepository->findOneBy([
            'title' => $value
        ]);
        if (!$existingTitle) {
            return;
        }
        /* @var $constraint UniqueTitle */
        $this->context->buildViolation($constraint->message)->addViolation();
    }

}
