# Laravel State Machine

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-FF2D20.svg)](https://laravel.com/)

Declarative finite state machine for Laravel domain workflows (orders, auctions, bookings) with transition guards, lifecycle hooks, and immutable audit history.

## Key Architectural Highlights

- **Fluent State Graph:** Define valid `from -> to` transitions cleanly in one place.
- **Transition Guards:** Block invalid domain actions dynamically using context-aware predicates.
- **Before & After Hooks:** Trigger notifications, ledger postings, or side-effects around state changes.
- **Audit Trail:** Automatically captures `from`, `to`, `actor`, `context`, and timestamps.

## Architecture & Lifecycle Workflow

```mermaid
stateDiagram-v2
    [*] --> pending
    pending --> paid: pay [guard passed]
    paid --> shipped: ship
    pending --> cancelled: cancel
    paid --> refunded: refund
```

## Installation & Setup

```bash
composer require nikhilakhiyaniya/laravel-state-machine
```

Publish the package configuration:

```bash
php artisan vendor:publish --tag="state-machine-config"
```

## Quick Start

```php
use NikhilAkhiyaniya\StateMachine\{StateGraph, StateMachine};

$graph = (new StateGraph())
    ->allow('pay', 'pending', 'paid')
    ->allow('ship', 'paid', 'shipped')
    ->guard('ship', fn ($from, $to, $ctx) => !empty($ctx['tracking_number']));

$fsm = new StateMachine($graph, 'pending');
$fsm->apply('pay', actor: 'customer:19');
```

## Testing

```bash
composer test
```

## Security & Contributing

If you discover a security vulnerability, please email `nikhilakhiyaniya@gmail.com` instead of using the public issue tracker. Pull requests with unit tests are welcome.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
