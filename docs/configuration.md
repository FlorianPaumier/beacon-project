# Configuration Reference

All configuration is set in `config/packages/beacon_admin.yaml` under the `beacon_admin` key.

## Top-Level Options

```yaml
beacon_admin:
    route_prefix: /admin
    title: 'Beacon Admin'
    menu:
        - { label: Dashboard, route: beacon_admin_dashboard, icon: home }
    theme:
        primary_color: '#2563eb'
        dark_mode: true
    security:
        admin_role: ROLE_ADMIN
        login_route: beacon_admin_login
        after_login_redirect: beacon_admin_dashboard
    widgets:
        - App\Admin\Widget\CustomWidget
    assets:
        enabled: true
```

## `route_prefix`

The URL prefix for all admin routes. Default: `/admin`.

## `title`

The page title shown in the browser tab and header bar. Default: `Beacon Admin`.

## `menu`

An array of menu items. Each item supports:

```yaml
menu:
    - label: Dashboard
      route: beacon_admin_dashboard
      icon: home
    - label: Products
      route: beacon_admin_product_list
      icon: box
      children:
          - label: All Products
            route: beacon_admin_product_list
          - label: Add Product
            route: beacon_admin_product_create
```

| Key | Type | Required | Description |
|-----|------|----------|-------------|
| `label` | string | yes | Display text |
| `route` | string | yes | Route name |
| `icon` | string | no | Icon identifier (Bootstrap Icons subset) |
| `role` | string | no | Required role for visibility |
| `children` | array | no | Nested menu items |

## `themes`

```yaml
themes:
    modern: 'bundles/beaconadmin/beacon-modern.css'
    enterprise: 'bundles/beaconadmin/beacon-enterprise.css'
    my-custom: 'bundles/myapp/my-theme.css'
default_theme: modern
```

Themes are swappable at runtime via the header dropdown. Each theme CSS file contains both light and dark color schemes — users toggle dark mode independently. Override any theme by providing a custom CSS path.

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `themes` | map | `{modern, enterprise, brut}` | Theme name → CSS file path |
| `default_theme` | string | `modern` | Initial theme (must be a key in `themes`) |

### Built-in themes

| Theme | Style | Primary |
|-------|-------|---------|
| `modern` | Clean, rounded, soft shadows (default) | Blue `#2563eb` |
| `enterprise` | Flat, dense, conservative, compact | Navy `#1e40af` |
| `brut` | High contrast, monospace, thick borders | Orange `#ea580c` |

### Custom theme

Create a CSS file with `[data-theme="my-custom"]` and `[data-theme="my-custom"].dark` blocks defining `--beacon-*` variables (copy one of the built-in files as a starting point). Point to it in config:

```yaml
themes:
    my-custom: 'bundles/myapp/my-theme.css'
default_theme: my-custom
```

## `security`

```yaml
security:
    admin_role: ROLE_ADMIN
    login_route: beacon_admin_login
    after_login_redirect: beacon_admin_dashboard
```

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `admin_role` | string | `ROLE_ADMIN` | Role required for admin access |
| `login_route` | string | `beacon_admin_login` | Login page route name |
| `after_login_redirect` | string | `beacon_admin_dashboard` | Redirect after login |

## `widgets`

An array of widget service IDs to register on the dashboard:

```yaml
widgets:
    - App\Admin\Widget\StatsWidget
    - App\Admin\Widget\RecentOrdersWidget
```

## `assets`

```yaml
assets:
    enabled: true
```

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `enabled` | bool | `true` | Enable AssetMapper integration |
