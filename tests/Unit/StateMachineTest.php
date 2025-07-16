<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine\Tests\Unit;

use NikhilAkhiyaniya\StateMachine\Exceptions\InvalidTransitionException;
use NikhilAkhiyaniya\StateMachine\Exceptions\TransitionGuardException;
use NikhilAkhiyaniya\StateMachine\StateGraph;
use NikhilAkhiyaniya\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;

class StateMachineTest extends TestCase
{
    public function test_applies_valid_transitions_and_records_history(): void
    {
        $events = [];
        $graph = (new StateGraph())
            ->allow('pay', 'pending', 'paid')
            ->allow('ship', 'paid', 'shipped')
            ->after('pay', function (string $from, string $to) use (&$events): void {
                $events[] = "{$from}->{$to}";
            });

        $fsm = new StateMachine($graph, 'pending');
        $fsm->apply('pay', ['tx' => 'abc'], 'user:42');

        $this->assertSame('paid', $fsm->state());
        $this->assertSame(['ship'], $fsm->availableTransitions());
        $this->assertSame(['pending->paid'], $events);
        $this->assertCount(1, $fsm->history());
        $this->assertSame('user:42', $fsm->history()[0]->actor);
    }

    public function test_blocks_transition_when_guard_fails(): void
    {
        $graph = (new StateGraph())
            ->allow('approve', 'draft', 'approved')
            ->guard('approve', fn (string $from, string $to, array $ctx): bool => ($ctx['verified'] ?? false) === true);

        $fsm = new StateMachine($graph, 'draft');
        $this->assertFalse($fsm->can('approve', ['verified' => false]));

        $this->expectException(TransitionGuardException::class);
        $fsm->apply('approve', ['verified' => false]);
    }

    public function test_rejects_disallowed_transition_from_current_state(): void
    {
        $graph = (new StateGraph())->allow('ship', 'paid', 'shipped');
        $fsm = new StateMachine($graph, 'pending');

        $this->expectException(InvalidTransitionException::class);
        $fsm->apply('ship');
    }
}
