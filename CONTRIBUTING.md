# Contributing to Beacon Admin

Thanks for your interest in contributing! This guide covers everything you need to get started.

## Development Setup

You'll need:

- PHP 8.4+
- Composer
- Symfony CLI (optional, for `symfony serve`)
- Bun (for asset builds)

```bash
git clone https://github.com/devgeek/beacon-admin.git
cd beacon-admin
composer install
bun install
bun run build
```

## Running Tests

```bash
# Full test suite
vendor/bin/phpunit

# With coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html var/coverage

# Mutation testing (optional, slow)
vendor/bin/infection
```

## Code Quality

We enforce the following tools. Run them before submitting a PR:

```bash
# Static analysis (level 2)
vendor/bin/phpstan analyse

# Coding standards (PSR-12 + Symfony)
vendor/bin/php-cs-fixer fix --dry-run --diff

# Auto-fix standards
vendor/bin/php-cs-fixer fix

# Twig linting
vendor/bin/twig-cs-fixer lint templates
```

## Project Structure

```
src/          # PHP bundle source (Devgeek\BeaconAdmin)
  Controller/ # CRUD controllers
  Crud/       # CRUD engine (CrudConfig, field mapping)
  Event/      # Lifecycle events (BeforeCreate, AfterUpdate, etc.)
  Form/       # Form types
  Menu/       # Sidebar navigation system
  Security/   # Auth + BeaconAccess attribute
  Table/      # Server-side datatable engine
  Twig/       # Twig extensions + runtime
  Widget/     # Dashboard widget system
assets/       # Frontend (Stimulus, SCSS, Tailwind)
config/       # Bundle service definitions + routes
docs/         # Documentation
tests/        # PHPUnit tests
translations/ # i18n (English + French)
```

## Pull Request Process

1. **Create a feature branch** from `main`
2. **Write or update tests** — new features need test coverage
3. **Run the full quality pipeline** — see "Code Quality" above
4. **Update docs** if you changed public API or behavior (files in `docs/`)
5. **Keep commits focused** — use conventional commit messages (`feat:`, `fix:`, `docs:`, etc.)
6. **Open a PR** against `main` with a clear description of what changed and why

## Coding Standards

- **PHP**: PSR-12 via PHP-CS-Fixer. Strict types (`declare(strict_types=1)`) in every file.
- **Twig**: snake_case variables, kebab-case block names. Lint with `twig-cs-fixer`.
- **PHPStan**: Level 2. All public methods must have parameter and return types.
- **Naming**: Controllers suffixed with `Controller`, services with their domain name (no `Service` suffix unless it's genuinely generic).

## Documentation

When adding or changing a public feature, update the corresponding file in `docs/`:

| Doc | When to update |
|-----|----------------|
| `getting-started.md` | Installation steps, first-run experience |
| `configuration.md` | New config keys or changed defaults |
| `crud.md` | CRUD engine changes |
| `widgets.md` | Widget API changes |
| `theming.md` | CSS variable or template changes |

## Need Help?

Open a [GitHub Discussion](https://github.com/devgeek/beacon-admin/discussions) or comment on your PR — we'll help get it merged.
