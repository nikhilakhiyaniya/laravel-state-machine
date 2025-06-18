<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine\Exceptions;

use DomainException;

class TransitionGuardException extends DomainException
{
    public static function blocked(string $transition, string $reason = 'Guard rejected transition.'): self
    {
        return new self("Transition [{$transition}] blocked: {$reason}");
    }
}
