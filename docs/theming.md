# Theming Guide

## CSS Custom Properties

The entire theme is controlled through CSS custom properties prefixed with `--beacon-`:

```css
:root {
    --beacon-primary: #2563eb;
    --beacon-primary-hover: #1d4ed8;
    --beacon-bg: #f8fafc;
    --beacon-surface: #ffffff;
    --beacon-text: #0f172a;
    --beacon-text-muted: #64748b;
    --beacon-border: #e2e8f0;
    --beacon-sidebar-bg: #1e293b;
    --beacon-sidebar-text: #f1f5f9;
    --beacon-sidebar-width: 260px;
    --beacon-header-height: 64px;
    --beacon-radius: 0.5rem;
    --beacon-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    --beacon-font: 'Inter', system-ui, -apple-system, sans-serif;
}
```

Override any property in your application stylesheet:

```css
:root {
    --beacon-primary: #7c3aed;
    --beacon-radius: 0.25rem;
    --beacon-sidebar-bg: #0f172a;
}
```

## Dark Mode

The bundle ships with a dark theme activated by the `data-theme="dark"` attribute on the `<html>` element. Dark mode variables automatically override the light palette:

```css
[data-theme="dark"] {
    --beacon-bg: #0f172a;
    --beacon-surface: #1e293b;
    --beacon-text: #f1f5f9;
    --beacon-text-muted: #94a3b8;
    --beacon-border: #334155;
}
```

Toggle dark mode programmatically:

```javascript
document.documentElement.setAttribute('data-theme', 'dark');
```

A Stimulus controller (`beacon--theme-toggle`) handles user preference persistence via `localStorage`.

## Overriding Templates

Override any bundle template by creating the same path in `templates/bundles/BeaconAdminBundle/`:

```
templates/bundles/BeaconAdminBundle/
    dashboard/
        index.html.twig
    crud/
        list.html.twig
    components/
        sidebar.html.twig
```

## Custom CSS

Add custom stylesheets in your application:

```twig
{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('styles/admin-overrides.css') }}">
{% endblock %}
```

### Class Naming Convention

All Beacon Admin CSS classes follow the pattern:

```
.beacon-{component}-{variant}
```

Examples:

| Class | Purpose |
|-------|---------|
| `.beacon-layout` | Main flex layout |
| `.beacon-sidebar` | Sidebar container |
| `.beacon-widget-grid` | Dashboard widget grid |
| `.beacon-widget--cols-6` | Six-column widget width |
| `.beacon-alert--success` | Success alert variant |
| `.beacon-table-header` | Sortable table header |
| `.beacon-btn--primary` | Primary action button |
| `.beacon-form-input` | Text input field |
