<?php

declare(strict_types=1);

namespace Test\Generator\Validator;

use Symfony\Component\Validator\Constraint;

class Dummy extends Constraint
{
    public function getTargets()
    {
        // or change to prop
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'dummy_validator';
    }
}
