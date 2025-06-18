<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine;

use DateTimeImmutable;

final class TransitionRecord
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly string $transition,
        public readonly string $from,
        public readonly string $to,
        public readonly ?string $actor,
        public readonly array $context,
        public readonly DateTimeImmutable $occurredAt,
    ) {
    }
}
