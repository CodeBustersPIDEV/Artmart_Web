<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class PasswordRequirementsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ length }}', 8)
                ->addViolation();
        }
    }
}
