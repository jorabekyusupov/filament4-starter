# SOLID (must follow)

## S — Single Responsibility Principle

A class must have only one reason to change. Keep each class focused on a single responsibility.

Do:
- Keep model logic in models, view logic in views, business logic in services.
- Create separate classes for validation, persistence, and side-effects (mail, logging, notifications).

Don't:
- Put DB, HTTP, and UI logic in the same class.

## O — Open/Closed Principle

Code should be open for extension but closed for modification.

Do:
- Add new behavior by extending with new classes or strategies.
- Use configuration, interfaces, and composition to add variants.

Don't:
- Keep adding conditionals into one class every time a new case appears.

## L — Liskov Substitution Principle

Subclasses must be substitutable for their base classes without breaking behavior.

Do:
- Ensure child classes respect the base class contract.
- Avoid throwing new exceptions or changing return types in overrides.

Don't:
- Extend a class if the child cannot fully act like the parent.

## I — Interface Segregation Principle

Clients should not depend on methods they do not use. Prefer small, focused interfaces.

Do:
- Split large interfaces into smaller ones (read vs write, cache vs query).

Don't:
- Force implementers to define unrelated methods.

## D — Dependency Inversion Principle

High-level modules should not depend on low-level modules. Both should depend on abstractions.

Do:
- Depend on interfaces (contracts) and inject implementations.
- Keep construction/wiring separate from usage (service providers, DI container).

Don't:
- Instantiate concrete classes inside core business logic.
