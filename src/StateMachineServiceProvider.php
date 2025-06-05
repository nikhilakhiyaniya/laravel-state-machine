<?php

declare(strict_types=1);

namespace NikhilAkhiyaniya\StateMachine;

use Illuminate\Support\ServiceProvider;

class StateMachineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/state-machine.php', 'state-machine');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/state-machine.php' => $this->app->configPath('state-machine.php'),
            ], 'state-machine-config');
        }
    }
}
