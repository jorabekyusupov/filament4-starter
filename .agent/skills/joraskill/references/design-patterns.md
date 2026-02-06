# Design Patterns (project-oriented, must follow)

## How to use in this project

Use these patterns as guidance for code in modules, services, repositories, and Filament resources.
Prefer small, testable classes and inject dependencies via the container.

## Creational patterns

### Factory / Abstract Factory

Use when creating objects based on config, type, or module.

Project mapping:
- Create factories for Notification, RPC client, or integration clients instead of `new` in business logic.
- Prefer `app()->make()` or a dedicated factory class in `modules/*/src/Services`.

Do:
- `NotificationFactory::make($type)`
- `IntegrationClientFactory::make($provider)`

### Builder

Use when an object needs many optional parameters.

Project mapping:
- Use `Filament\Tables\Table` and `Schema` builders as done in `ApplicationsTable`, `ApplicationForm`, `ApplicationInfolist`.

### Prototype

Use for cloning complex objects.

Project mapping:
- Rare; only if you need to clone a configured object (e.g., repeated schema sections).

## Structural patterns

### Adapter

Use to wrap third-party APIs or legacy code.

Project mapping:
- Wrap external RPC/HTTP clients behind an interface.
- Keep adapters in `modules/*/src/Services` or `modules/*/src/Libraries`.

### Facade

Use for simple access to complex subsystems.

Project mapping:
- Laravel facades are already used (`Cache`, `Http`, `Log`).
- Create your own facade only when it hides a complex subsystem and is stable.

### Decorator

Use to extend behavior without changing original class.

Project mapping:
- Wrap repositories to add caching or logging.
- Example: `CachedApplicationRepository` wraps `ApplicationRepository`.

### Composite

Use for tree-like structures.

Project mapping:
- Use for nested form sections or hierarchical data if needed.

### Proxy

Use to control access to heavy or remote objects.

Project mapping:
- Lazy-loading for remote clients or deferred execution.

## Behavioral patterns

### Strategy

Use when multiple algorithms are interchangeable.

Project mapping:
- Payment logic, import/export strategies, or translation sources.
- Choose strategy by config or runtime parameter.

### Command

Use to encapsulate actions.

Project mapping:
- Artisan commands, queued jobs, or action classes.

### Observer

Use for event-driven logic.

Project mapping:
- Laravel events/listeners for module-specific side effects.

### State

Use when behavior depends on state.

Project mapping:
- Order/application lifecycle states (draft, active, archived).

### Chain of Responsibility

Use to process requests through handlers.

Project mapping:
- Middleware pipeline or validation chains.

### Template Method

Use to define a common process with custom steps.

Project mapping:
- Base service classes with overridable steps.

## Rules

- Prefer composition over inheritance.
- Prefer Strategy/Factory over long `switch`/`if` chains.
- Keep external dependencies behind adapters or interfaces.
- Do not instantiate concrete classes directly inside core business logic; inject them.
