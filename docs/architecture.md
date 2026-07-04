# Architecture

How a generated value is produced on save. See the [Documentation index](../README.md#documentation).

## The moving parts

- **Traits** (`Traits/HasSku`, `HasOrderNumber`, `HasTicketNumber`) — added to a model; each boots a model
  observer for its destination column.
- **Observer** (`Generators/Services/Observer`) — hooks the model's create/update events and asks the
  generator for a value.
- **Generator** (`Generators/Services/Generator` + per-type `Concerns/*Generator`) — builds the value from
  the configured source column(s) + separator; enforces uniqueness when required. Custom generators extend
  these (see [Generators](tools/generators.md)).
- **Configs** (`Generators/Services/Configs`) — merges the `generator.default` block with the per-generator
  block and any per-model overrides.
- **Contracts** (`Generators/Contracts/*GeneratorInterface`) — the interface a custom generator implements.

## Flow

1. A model using a generator trait is saved.
2. The observer fires on create (and on update when `refresh_on_update` is set).
3. The generator reads the source column(s), joins them with the separator, and — if `unique` — ensures no
   collision, writing the result to the destination column.

---

[← Docs index](../README.md#documentation)
