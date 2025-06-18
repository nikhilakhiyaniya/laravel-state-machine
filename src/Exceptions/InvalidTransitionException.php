<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine\Exceptions;

use DomainException;

class InvalidTransitionException extends DomainException
{
    public static function cannotApply(string $transition, string $currentState): self
    {
        return new self("Transition [{$transition}] is not allowed from state [{$currentState}].");
    }
}
