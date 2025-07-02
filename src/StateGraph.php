<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine;

final class StateGraph
{
    /** @var array<string, array{from: list<string>, to: string}> */
    private array $transitions = [];

    /** @var array<string, list<callable>> */
    private array $guards = [];

    /** @var array<string, list<callable>> */
    private array $beforeHooks = [];

    /** @var array<string, list<callable>> */
    private array $afterHooks = [];

    /**
     * @param string|list<string> $from
     */
    public function allow(string $transition, string|array $from, string $to): self
    {
        $this->transitions[$transition] = [
            'from' => is_array($from) ? array_values($from) : [$from],
            'to' => $to,
        ];

        return $this;
    }

    public function guard(string $transition, callable $guard): self
    {
        $this->guards[$transition][] = $guard;
        return $this;
    }

    public function before(string $transition, callable $hook): self
    {
        $this->beforeHooks[$transition][] = $hook;
        return $this;
    }

    public function after(string $transition, callable $hook): self
    {
        $this->afterHooks[$transition][] = $hook;
        return $this;
    }

    /** @return array{from: list<string>, to: string}|null */
    public function definitionFor(string $transition): ?array
    {
        return $this->transitions[$transition] ?? null;
    }

    /** @return list<string> */
    public function transitionsFrom(string $state): array
    {
        $available = [];
        foreach ($this->transitions as $name => $def) {
            if (in_array($state, $def['from'], true)) {
                $available[] = $name;
            }
        }
        return $available;
    }

    /** @return list<callable> */
    public function guardsFor(string $transition): array
    {
        return $this->guards[$transition] ?? [];
    }

    /** @return list<callable> */
    public function beforeHooksFor(string $transition): array
    {
        return $this->beforeHooks[$transition] ?? [];
    }

    /** @return list<callable> */
    public function afterHooksFor(string $transition): array
    {
        return $this->afterHooks[$transition] ?? [];
    }
}
