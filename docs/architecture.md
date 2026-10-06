# Architecture

How a generated value is produced on save. See the [Documentation index](../README.md#documentation).

## The moving parts

- **Traits** (`Traits/HasSku`, `HasOrderNumber`, `HasTicketNumber`) — added to a model; each registers its
  observer's `creating` and `updating` handlers for its destination column.
- **Observer** (`Generators/Services/Observer`) — hooks the model's create/update events and asks the
  generator for a value.
- **Generator** (`Generators/Services/Generator` + per-type `Concerns/*Generator`) — builds the value from
  the configured prefix, source column(s) and separator; enforces uniqueness when required. The per-type
  generators are final; a custom generator extends the non-final `Generator` base or implements the
  interface (see [Generators](tools/generators.md#custom-generators)).
- **Configs** (`Generators/Services/Configs`) — merges the `generator.default` block with the per-generator
  block and any per-model overrides.
- **Contracts** (`Generators/Contracts/*GeneratorInterface`) — the per-type interfaces the container resolves
  a generator through. Each extends `Generators/Services/Contracts/GeneratorInterface`, whose `render()` the
  observer calls. The provider builds the class a config block names as `new $class($model)` and rejects one
  that does not implement `GeneratorInterface`.

## Flow

1. A model using a generator trait is saved.
2. The observer fires on create (and on update when `refresh_on_update` is set).
3. The observer calls the generator's `render()`. The shipped generators read the source column(s), join them
   with the separator, lead with the prefix when one is configured, and — if `unique` — retry until the
   value is not already in the destination column. The observer writes the result there.

---

[← Docs index](../README.md#documentation)
