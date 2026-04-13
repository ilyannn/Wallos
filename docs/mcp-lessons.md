# MCP Lessons From `wallos-mcp`

## Short History

This project started as an MCP server for Wallos, intended to let Claude
Desktop manage subscriptions in a self-hosted Wallos instance.

The early emphasis was on repository setup and delivery infrastructure:
TypeScript, Bun, linting, formatting, CI, Docker, and test scaffolding. The
initial planning documents and README described a broad roadmap, including
subscription management, master data CRUD, analytics, exports, and admin or
settings-related read tools.

The implementation that actually landed was narrower. The server now exposes a
working core set of tools for:

- reading master data
- listing subscriptions
- managing categories
- creating subscriptions
- editing subscriptions

Over time, development effort concentrated less on MCP wiring itself and more
on making Wallos integration behave correctly. The hard parts turned out to be
session-based authentication, API-key bootstrapping, real-environment
compatibility, and getting E2E tests to behave consistently against live
instances.

In its current state, the project is a real MCP integration and a usable MVP,
but not a fully finished Wallos platform surface. It succeeded at shipping a
core bridge, while several planned tools and some real-instance reliability
work remain incomplete.

## Main Lessons

### 1. Keep MCP Thin

MCP should be a transport layer over well-defined application services, not the
main home for business logic.

In this project, too much logic accumulated in the Wallos client and tool
handlers: entity lookups, auto-creation, billing-period parsing, fallback
behavior, and response formatting. That made the MCP adapter do more than just
translate requests.

For a new implementation, the subscription tracker should expose internal
domain services first, and MCP should call those same services directly.

### 2. Use One Authentication Model

The biggest integration pain here came from combining:

- session login
- cookie management
- API-key injection
- API-key regeneration from an authenticated session

That works, but it is brittle. A new tracker should use one explicit auth model
for both the UI/API and MCP, ideally a token-based approach with clear
permissions and predictable expiry behavior.

### 3. Design for Machine Callers First

MCP tools are easiest to use when inputs are strict and outputs are structured,
stable, and easy to validate.

This project often returns human-friendly text, which is convenient in a chat
client but not ideal as the primary contract. In a new system, every tool
should have:

- strict schemas
- stable success payloads
- typed error codes
- predictable mutation results

Formatted prose should be a presentation choice, not the base protocol.

### 4. Make Side Effects Explicit

One of the attractive features here is automatic creation of related entities
like categories, payment methods, currencies, and household members.

That convenience comes with ambiguity. Tool callers may intend to reference an
existing entity, but instead create a new one by typo or mismatch.

For a new version, creation should be explicit or controlled by flags such as
`create_if_missing`, so the caller chooses whether the operation is pure lookup
or mutation-capable.

### 5. Build Idempotent Mutations

MCP clients may retry. Agents may retry. Network layers may retry.

If `create_subscription` is not idempotent, retries can create duplicates or
produce unclear state. A better design is to support idempotency keys, unique
constraints, or explicit upsert semantics where appropriate.

### 6. Support the Full Lifecycle

A tracker is easier to operate and test when it supports the full lifecycle of
its main entities:

- create
- read
- update
- disable or cancel
- delete

This project reached create and edit for subscriptions, but cleanup and full
lifecycle support remained weaker. That made tests and operational maintenance
more awkward than they should be.

### 7. Treat Real Integration Tests as a Release Gate

This repo has strong unit and mocked test coverage, but the remaining failures
cluster in live-environment and E2E paths. That is an important signal.

For an MCP-native product, the main confidence bar is not "the handlers work in
isolation." It is "the tool works against the real service with realistic auth,
state, and timing."

Build a hermetic integration environment early and make it part of the default
definition of done.

### 8. Plan for Read-After-Write Consistency

Many tests create a subscription and then immediately list subscriptions to
verify it. That pattern is normal for MCP tools.

The backend should guarantee that a successful write is visible to immediate
reads, or return enough authoritative state in the mutation response that the
caller does not need an immediate follow-up fetch.

### 9. Keep the Docs Honest

The README and planning docs describe a much broader product than the current
server exposes.

That is common in early-stage projects, but for MCP integrations it creates
confusion quickly because users treat tool availability as the product.

Documentation should clearly separate:

- shipped tools
- experimental tools
- planned tools

### 10. Isolate Upstream Quirks Behind a Compatibility Layer

Wallos imposed several constraints and quirks:

- a session-oriented auth flow
- API-key regeneration as part of access bootstrapping
- incomplete or awkward mutation coverage
- behavior that tests had to accommodate

Those are exactly the kinds of issues a compatibility layer should absorb. If
you are building a new subscription tracker, avoid leaking backend oddities
into MCP semantics. Keep the MCP contract clean even if the internal
implementation needs adapters.

## What To Carry Forward Into a New MCP-Native Subscription Tracker

- Build the core product first: domain model, auth, persistence, and service
  layer.
- Add MCP as a first-party transport over those same services.
- Use one auth system for both humans and agents.
- Return structured mutation results with canonical IDs and normalized objects.
- Make mutations idempotent and side effects explicit.
- Support cleanup and deletion paths from the start.
- Keep a small, stable v1 tool surface before expanding into analytics and
  exports.
- Treat live integration tests as mandatory, not optional hardening work.

## Bottom Line

The main lesson from `wallos-mcp` is that building the MCP server itself was
not the hard part. The difficult part was creating a reliable, machine-friendly
integration boundary on top of an application that was not originally designed
around MCP-style automation.

If you build a new subscription tracker in a different language, the best move
is not just to rewrite the adapter. It is to design the product so that MCP is
a natural transport from day one.
