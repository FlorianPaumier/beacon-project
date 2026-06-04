# Changelog

All notable changes to Beacon Admin are documented in this file.

## [1.0.0] — 2026-06-04

### First Stable Release

Beacon Admin is a lightweight, modern admin dashboard bundle for Symfony 7.4+ and 8.0+. Built with Twig, Stimulus, and Turbo — no Webpack, no Livewire, no Alpine.js.

### Core Features

- **CRUD Engine** — Auto-generated list, create, edit, and delete views from Doctrine entities. Configurable columns, sortable headers, server-side pagination, and inline boolean toggles.
- **Security Layer** — Role-based access control with fail-closed voters, custom permissions, and field-level security.
- **Menu System** — Configurable navigation with grouped menu items, theming, and route prefixes.
- **Theming** — Built-in light/dark themes plus custom theme support via configuration.
- **Bulk Operations** — Row selection with select-all and bulk delete support.
- **Filtering** — Rich data table filters (text, boolean, date, choice) with server-side processing.
- **Notifications** — Toast lifecycle, notification bell widget, and optional database persistence.
- **Breadcrumbs** — Automatic breadcrumb generation with deep linking.
- **Form Enhancements** — Searchable association fields, enum/choice field support, and field-level security.
- **Dashboard Widgets** — Configurable dashboard with extensible widget system.

### Technical Quality

- **0 PHPStan errors** at level 6 (strict rules, Doctrine, Symfony, PHPUnit extensions)
- **1175 tests passing** with 2189 assertions
- PHP 8.4+ with Symfony 7.4/8.0 compatibility
- Doctrine ORM 2.20+/3.6+ and DBAL 3.10+/4.4+ support
- MIT licensed
