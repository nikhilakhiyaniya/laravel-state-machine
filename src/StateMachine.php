<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine;

use DateTimeImmutable;
use NikhilAkhiyaniya\StateMachine\Exceptions\InvalidTransitionException;
use NikhilAkhiyaniya\StateMachine\Exceptions\TransitionGuardException;

final class StateMachine
{
    private string $currentState;

    /** @var list<TransitionRecord> */
    private array $history = [];

    public function __construct(
        private readonly StateGraph $graph,
        string $initialState,
    ) {
        $this->currentState = $initialState;
    }

    public function state(): string
    {
        return $this->currentState;
    }

    /** @param array<string, mixed> $context */
    public function can(string $transition, array $context = []): bool
    {
        $def = $this->graph->definitionFor($transition);
        if ($def === null || !in_array($this->currentState, $def['from'], true)) {
            return false;
        }

        foreach ($this->graph->guardsFor($transition) as $guard) {
            if ($guard($this->currentState, $def['to'], $context) === false) {
                return false;
            }
        }

        return true;
    }

    /** @return list<string> */
    public function availableTransitions(): array
    {
        return $this->graph->transitionsFrom($this->currentState);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function apply(string $transition, array $context = [], ?string $actor = null): string
    {
        $def = $this->graph->definitionFor($transition);
        if ($def === null || !in_array($this->currentState, $def['from'], true)) {
            throw InvalidTransitionException::cannotApply($transition, $this->currentState);
        }

        $from = $this->currentState;
        $to = $def['to'];

        foreach ($this->graph->guardsFor($transition) as $guard) {
            if ($guard($from, $to, $context) === false) {
                throw TransitionGuardException::blocked($transition);
            }
        }

        foreach ($this->graph->beforeHooksFor($transition) as $hook) {
            $hook($from, $to, $context);
        }

        $this->currentState = $to;
        $this->history[] = new TransitionRecord(
            transition: $transition,
            from: $from,
            to: $to,
            actor: $actor,
            context: $context,
            occurredAt: new DateTimeImmutable(),
        );

        foreach ($this->graph->afterHooksFor($transition) as $hook) {
            $hook($from, $to, $context);
        }

        return $this->currentState;
    }

    /** @return list<TransitionRecord> */
    public function history(): array
    {
        return $this->history;
    }
}
