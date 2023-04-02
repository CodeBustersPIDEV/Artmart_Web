<?php

namespace App\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NotFutureDate extends Constraint
{
    public $message = 'This date should not be in the future.';

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}

class NotFutureDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value > new \DateTime()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
