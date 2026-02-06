# OOP Principles (must follow)

## Core concepts

- Class: blueprint of data + behavior.
- Object: instance of a class.

## Encapsulation

Keep state private and expose controlled methods.

Do:
- Use private/protected properties and explicit getters/setters when needed.
- Validate and normalize data at boundaries.

Don't:
- Expose mutable state directly.

## Abstraction

Hide internal complexity and expose simple, stable interfaces.

Do:
- Keep public APIs small and focused.
- Use service classes to hide infra details.

Don't:
- Leak implementation details in public methods.

## Inheritance

Reuse behavior by extending only when the child truly "is-a" parent.

Do:
- Extend base classes only when behavior is substitutable.

Don't:
- Use inheritance to reuse unrelated code.

## Polymorphism

Use a common interface with multiple implementations.

Do:
- Depend on contracts (interfaces) and inject concrete types.

Don't:
- Branch on type with long conditionals.

## Composition over inheritance

Prefer composing objects instead of deep inheritance trees.

Do:
- Inject dependencies (services, repositories, strategies).

Don't:
- Build fragile, multi-level inheritance chains.

## Relationships

- Association: general relationship between objects.
- Aggregation: "has-a" where child can outlive parent.
- Composition: strong ownership; child lives/dies with parent.

## Design rules

- Keep classes small and cohesive.
- Keep method responsibilities single and clear.
- Avoid global state and static side effects.
- Keep dependencies explicit via constructor injection.
