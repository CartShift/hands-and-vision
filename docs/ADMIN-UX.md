# Hand and Vision — Client Admin Experience

A single-file module that turns the generic WordPress admin into a focused,
branded environment for the gallery's content team.

**File:** `inc/admin/class-hv-admin.php`
**Loaded from:** `functions.php` (always loaded; hooks only fire in admin/login)

---

## What the client sees

### Login screen (`wp-login.php`)
- Dark "gallery" palette (`#0e0e10` background, `#c9a96e` gold accent)
- HV wordmark logo replaces the WordPress logo (uses `assets/images/hv-wordmark.svg`)
- Header link points to the site home (not wordpress.org)
- Auto-RTL when the site language is Hebrew
- "Privacy Policy" link hidden (visual noise)

### Dashboard (`/wp-admin/`)
- **Welcome widget** (`hv_welcome`) — bilingual greeting + 6–8 quick-action cards
  to the most common tasks: add gallery item / artist / service / product,
  view orders, edit pages, customize, view site.
- **Store snapshot widget** (`hv_store_snapshot`, only when WooCommerce active) —
  orders today, awaiting processing, out-of-stock count, each linking to the
  filtered list.
- **Removed:** WordPress News, Quick Draft, At a Glance default activity,
  Site Health, the WP welcome panel, plus all promotional widgets matching
  `astra*`, `bsf*`, `elementor*`, `monsterinsights*`, `rank_math*`, `wpforms*`,
  `rg_forms*`, and WC recent-reviews.

### Admin menu
- Custom order: **Dashboard → Gallery → Artists → Services → Products → Pages → Posts → Media → Comments → …everything else**
- Parent-theme noise hidden from non-admins: `astra`, `theme-builder-free`,
  `astra-advanced-hook`.
- In Hebrew, the **Posts** menu reads "יומן / חדשות" (journal / news) to make
  its purpose obvious vs. CPTs.
- Admin bar: WP logo, comments node, customize, and updates removed for editors.

### CPT list tables (Artists, Services, Gallery)
- New **Image** column with a 56×56 thumbnail (clickable, opens edit screen).
- Placeholder icon when no featured image is set.
- `menu_order` registered as sortable so the client can sort by display order.

### Admin footer
- "Content Management — *Site Name* © YYYY" (bilingual) instead of
  "Thank you for creating with WordPress".
- Version string replaced with `HV {HV_THEME_VERSION}`.

### Notices
- All third-party admin notices are silenced for editors (no nags).
- Admins still see core/WP notices but the BSF analytics opt-in is suppressed.

### Login redirect
- Non-admins land on `/wp-admin/index.php` (the curated dashboard) instead of
  whatever WordPress would have picked.

### Admin color scheme — "Hand and Vision"
Registered via `wp_admin_css_color()` and shipped as
`admin/assets/css/hv-admin-color-scheme.css`.

Palette:

| Token       | Value     | Role                              |
|-------------|-----------|-----------------------------------|
| `--hv-bg`   | `#0e0e10` | Admin bar background              |
| Sidebar     | `#17171a` | Admin menu background             |
| Submenu     | `#1f1f23` | Open submenu / hover background   |
| `--hv-accent` | `#c9a96e` | Gold — links, focus, primary CTA |
| `--hv-fg`   | `#f4f4f2` | Foreground text                   |

Behaviour:
- New users auto-receive the scheme on registration (`user_register` hook
  writes `admin_color = 'hand-and-vision'`).
- Existing users who have never changed their scheme are silently switched
  via the `get_user_option_admin_color` filter — they still see "Hand and
  Vision" pre-selected under **Users → Profile** and can opt out by picking
  another scheme; their choice sticks (the filter only overrides when no
  user-meta value is set).
- Available alongside the WordPress defaults (Default / Light / Modern /
  Blue / Coffee / Ectoplasm / Midnight / Ocean / Sunrise) so users can
  switch freely.

---

## Bilingual strings

All UI strings go through `HV_Admin::t($he, $en)` which delegates to
`handandvision_is_hebrew()` (defined in `inc/accessibility/language-rtl.php`).
This matches the rest of the theme's `$is_hebrew ? 'HE' : 'EN'` pattern.

---

## Roles & capabilities

The module **does not** define custom roles. It keeps the WordPress defaults
(Administrator / Editor / Author / Contributor) and only differentiates by
`current_user_can( 'manage_options' )` for hiding promo pages and notices.

If the client wants role-restricted access in the future, this is the place
to wire it (e.g., a "Gallery Manager" role with capabilities for the three
CPTs + WooCommerce shop_manager).

---

## Extending

Add a new quick-action card to the welcome widget:

```php
add_filter( 'hv_admin_welcome_cards', function( $cards ) {
    $cards[] = [
        'icon'  => 'email-alt',
        'label' => 'Newsletter',
        'url'   => admin_url( 'admin.php?page=mailpoet' ),
    ];
    return $cards;
} );
```

> Note: the filter is **not** currently wired. If you need it, add
> `apply_filters( 'hv_admin_welcome_cards', $cards )` inside
> `render_welcome_widget()` before the foreach.

---

## Removal / disable

Comment out (or delete) this line in `functions.php`:

```php
require_once ASTRA_THEME_DIR . 'inc/admin/class-hv-admin.php';
```

No DB writes happen, so the admin returns to the default Astra/WordPress
experience instantly.
