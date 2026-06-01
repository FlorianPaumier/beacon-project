# RULE.md — Symfony Filament Clone

## Objective

Build a Symfony composer package that clones Filament's admin panel architecture. Target: **Symfony 7.x + Turbo/Stimulus + Twig + Tailwind CSS**. Deliver as a monorepo of standalone packages installable individually or together.

## Golden Rule

**Port the architecture, not the code.** Filament is tightly coupled to Laravel's service container, Eloquent, Livewire, and Blade. The Symfony clone must replace each coupling with its Symfony-native equivalent while preserving the developer experience: declarative, fluent, extensible, no frontend SPA.

---

## Framework Mapping

| Filament Concept | Symfony Equivalent |
|-----------------|-------------------|
| Service Container (`app()`) | Symfony DI (`$container->get()`) |
| Eloquent ORM | Doctrine ORM |
| Livewire (reactive components) | Turbo Frames + Stimulus controllers |
| Blade templates | Twig templates |
| Laravel routes | Symfony routing (attributes or YAML) |
| Artisan commands | Symfony Console commands |
| Laravel Notifications | Symfony Notifier |
| Laravel Queue | Symfony Messenger |
| Laravel Policies/Gates | Symfony Voters |
| Laravel Validation | Symfony Validator |
| Laravel Translations | Symfony Translation (`.xliff`/`.yaml`) |

---

## Package Map (mirror Filament's structure)

```
symfony-filament/
  support/           # Base component class, helpers, container utils
  schemas/           # Component tree engine (replace Livewire with Turbo)
  forms/             # Form component builder
  tables/            # Data tables with Doctrine pagination
  actions/           # Modal actions, bulk ops, import/export
  infolists/         # Read-only record display
  notifications/     # Flash messages + persisted notifications
  widgets/           # Dashboard widgets
  panels/            # Admin panel shell (auth, menus, resources)
  query-builder/     # Doctrine query builder UI
  maker-bundle/      # Maker commands (make:filament-resource, etc.)
```

Each package is a standalone `symfony/filament-*` Composer package with its own `composer.json`.

---

## Core Architecture Rules

### 1. Component Base Class

Every UI component extends `Filament\Support\Component`:

```php
namespace Filament\Support;

abstract class Component
{
    // Fluent builder: static factory + chainable methods
    public static function make(): static;
    
    // Closures are resolved lazily — the secret sauce
    public function evaluate(mixed $value): mixed;
    
    // All properties are nullable, have a setter, and a getter
    protected string|Closure|null $label = null;
    public function label(string|Closure|null $label): static;
    public function getLabel(): ?string;
}
```

**Rules**:
- No `final` classes — everything is extensible
- No `readonly` classes — users subclass everything
- Property + setter share the same name (no `setX()` prefix)
- Getter is always `get` + PascalCase property name
- Boolean properties: `isDisabled` → `disabled()` setter, `isDisabled()` getter
- All setters return `$this` for fluent chaining
- Use `evaluate()` in getters to support both literal values and closures

### 2. Schema System

The schema is the component tree. It replaces Filament's Livewire dependency with Symfony Turbo:

```php
// Filament: Livewire component with $schema
// Symfony: Twig component with embedded Turbo frame

class FormSchema
{
    /** @param array<Component> $components */
    public function schema(array $components): static;
    
    // State management: Turbo stream updates instead of Livewire re-renders
    public function getState(): array;
    public function fill(array $state): static;
}
```

**Rules**:
- Each schema renders as a `<turbo-frame>`
- State changes emit Turbo Stream responses (not full-page Livewire re-renders)
- Stimulus controllers handle client-side interactivity (Alpine.js replacement)
- Component IDs map to Turbo frame IDs

### 3. Doctrine Integration

Replace Eloquent with Doctrine ORM:

```php
// Filament: Eloquent\Model
// Symfony: Doctrine Entity

#[AsResource]
class UserResource extends Resource
{
    // Instead of Eloquent query scopes, use Doctrine repositories
    protected static function getEntityClass(): string
    {
        return App\Entity\User::class;
    }
    
    // Table columns, form fields — same API, Doctrine underneath
    public static function table(Table $table): Table;
    public static function form(Form $form): Form;
}
```

**Rules**:
- All queries go through Doctrine repositories, never raw DQL in component code
- Pagination uses Doctrine's `Paginator` (not Eloquent's `LengthAwarePaginator`)
- Relationships: Doctrine associations replace Eloquent relations
- `EntityRepository::findAll()` is never called unfiltered on tables

### 4. Turbo + Stimulus (Livewire Replacement)

Filament uses Livewire for server-driven reactivity. Symfony clone uses Turbo + Stimulus:

| Filament | Symfony Clone |
|----------|--------------|
| `wire:model` | Stimulus `data-action="input->form#sync"` |
| `wire:click` | Turbo Frame navigation or Stimulus action |
| Modal `wire:open` | Stimulus controller opening `<dialog>` |
| `$this->dispatch()` | Turbo Stream broadcast or Mercure event |
| `#[Locked]` | Stimulus values with `static: true` |

**Rules**:
- Stimulus controllers are lightweight — logic lives server-side
- Turbo Streams handle partial page updates (form validation, table reloads)
- Use Mercure Hub for real-time notifications (optional, fallback to polling)
- Each Filament component gets a corresponding Stimulus controller

### 5. Twig Component System

Replace Blade with Twig components:

```twig
{# Filament Blade: @livewire('filament.forms.text-input') #}
{# Symfony Twig: <twig:Filament:TextInput name="email" /> #}

{# Hook classes preserved: CSS is framework-agnostic #}
<div class="fi-fo-field fi-fo-text-input">
    {{ component.render() }}
</div>
```

**Rules**:
- One Twig component per Filament UI component
- Hook CSS classes (`fi-fo-*`, `fi-ta-*`) are preserved identically — the CSS is portable
- No Tailwind classes in Twig templates — use `@apply` in CSS with hook classes
- Twig component PHP classes extend the base `Component` class

### 6. Package Interoperability

Each package is independent but composable:

```json
// composer.json for symfony/filament-forms
{
    "require": {
        "php": "^8.2",
        "symfony/filament-support": "^1.0",
        "symfony/filament-schemas": "^1.0",
        "doctrine/orm": "^3.0",
        "symfony/twig-bundle": "^7.0",
        "symfony/ux-turbo": "^2.0"
    },
    "suggest": {
        "symfony/filament-actions": "Add modal actions to forms",
        "symfony/filament-tables": "Use forms within table filters"
    }
}
```

**Rules**:
- `support` is the only required dependency for all packages
- `schemas` depends on `support`
- `forms`, `infolists`, `tables` depend on `schemas`
- `panels` depends on everything
- Plugin packages suggest optional integrations (MediaLibrary, Tags, Settings)

---

## Developer Experience Rules

### Fluent API is Sacred

The Filament DX is defined by its fluent API. The Symfony clone must feel identical:

```php
// This exact developer experience must work in Symfony
TextInput::make('email')
    ->label('Email address')
    ->email()
    ->required()
    ->maxLength(255)
    ->unique(ignoreRecord: true),
```

### Naming Conventions (From CLAUDE.md)

- **Full names only**: `$exception`, not `$e`; `$configuration`, not `$cfg`
- **Backtick code references** in test names and comments
- **Gerund headings** in docs: "Setting the type", "Enabling search"
- **Use `app()` not `new`**: `$container->get(ComponentClass::class)`, not `new ComponentClass()`

### Testing

```bash
# Symfony equivalent of composer test
php bin/phpunit
php bin/phpstan analyse
php bin/php-cs-fixer fix --dry-run

# Test structure mirrors Filament
tests/
  Unit/
    Forms/Components/TextInputTest.php
    Tables/Columns/TextColumnTest.php
  Functional/
    Panels/ResourceTest.php
```

**Rules**:
- Unit tests for every component
- Functional tests for panel pages
- Browser tests (Panther) for UI components
- PHPStan level 6 minimum
- Test names use backticks: `` it('renders `email()` input with validation') ``

---

## What NOT to Port

1. **Laravel-specific features**: Policies, Notifications, Queues — use Symfony equivalents
2. **Livewire internals**: Don't reimplement Livewire; use Turbo/Stimulus
3. **Eloquent patterns**: Don't emulate `whereHas`, `withCount` — use Doctrine DQL
4. **Blade directives**: Port the component, not `@livewire` / `@filamentScripts`
5. **Spatie plugins**: Port the architecture, not the Spatie-specific implementations
6. **Upgrade package**: Write fresh Symfony migration tooling when v1→v2 happens

---

## Phase 1 Deliverables (MVP)

| Package | Contents |
|---------|----------|
| `support` | `Component` base class, `evaluate()`, helpers, DI utilities |
| `schemas` | Schema engine, component tree, Turbo frame rendering, Twig integration |
| `forms` | TextInput, Select, Checkbox, Toggle, FileUpload, RichEditor, validation |
| `tables` | Table builder, TextColumn, filters, pagination, Doctrine adapter |
| `panels` | Panel config, routing, auth (Symfony Security), menu builder |
| `maker-bundle` | `make:filament-resource`, `make:filament-panel` console commands |

## Phase 2

| Package | Contents |
|---------|----------|
| `actions` | Modal actions, bulk operations, import/export with Messenger |
| `infolists` | Read-only record views |
| `notifications` | Flash + database notifications via Symfony Notifier |
| `widgets` | Dashboard stats, chart components |
| `query-builder` | Doctrine query builder UI with filter constraints |

---

## Key Differences From Filament

1. **Symfony Flex** instead of Laravel's auto-discovery for bundle registration
2. **YAML/XML config** option alongside PHP config (Symfony convention)
3. **No `global_helpers.php`** — Symfony uses dependency injection, not global functions
4. **Mercure** for real-time updates (optional; polling fallback)
5. **Symfony UX** integration: use `symfony/ux-turbo`, `symfony/ux-stimulus`, `symfony/ux-twig-component`
6. **Doctrine entities** are not `Authenticatable` — User entity implements `Symfony\Component\Security\Core\User\UserInterface`

---

## Commit Convention

Follow Filament's commit style:
- Present tense: "Add feature", "Fix bug"
- Package prefix: `forms:`, `tables:`, `panels:`, `support:`
- Reference issues with `#123`
