# Hands and Vision Concept Store

## 🤖 AI / LLM Context

If you are an AI assistant working on this codebase, please consult the context files in the `_ai_context/` directory for detailed information:

- [Project Overview](_ai_context/00-PROJECT_OVERVIEW.md)
- [Tech Stack](_ai_context/01-TECH_STACK.md)
- [Coding Standards](_ai_context/02-CODING_STANDARDS.md)
- [Project Structure](_ai_context/03-PROJECT_STRUCTURE.md)

## Overview

Hands and Vision is a premium WordPress theme for an art collective, featuring a portfolio, services, and a WooCommerce store.

## Standards

Theme follows WordPress Coding Standards (escaping output, sanitization, nonce verification) and WooCommerce theme best practices (add_theme_support on after_setup_theme, product meta via WC_Product::get_meta where applicable).

## Quick Start

1. **Install Dependencies**: `npm install` (for tools/testing).
2. **WooCommerce Setup**: See [WOOCOMMERCE-INTEGRATION.md](WOOCOMMERCE-INTEGRATION.md).
3. **Site URL fix (after migration)**: If you moved from `https://ggr.zmk.mybluehost.me/website_8422dc8c`, enable `WP_DEBUG`, log in as admin, and visit `?hv_fix_url=1` on any front-end page to replace the old URL in the database.

4. **Maintenance mode**: Disabled by default. To enable before launch, add to `wp-config.php`:
   ```php
   define( 'HV_MAINTENANCE_MODE', true );
   define( 'HV_MAINTENANCE_PASSWORD', 'your-secure-password' );
   ```

## Local Development

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/) (running).

| Command | Description |
|---|---|
| `npm run dev` | Start WordPress at http://127.0.0.1:8888 |
| `npm run dev:stop` | Stop containers |
| `npm run dev:destroy` | Remove containers and database |
| `npm run dev:import -- file.wpress` | Import a backup from `backups/` |
| `npm run dev:polylang` | Configure Polylang (Hebrew default + English `/en/`) |
| `npm run dev:cli -- user list` | Run WP-CLI |

**Use http://127.0.0.1:8888** (not `localhost`) on Windows — Docker port forwarding can hang on `localhost`.

**Import from production:** Copy your `.wpress` export into `backups/`, then run `npm run dev:import -- your-file.wpress`. Log in at http://127.0.0.1:8888/wp-admin with your **production admin account**.

Maintenance mode is automatically disabled locally (`WP_ENVIRONMENT_TYPE=local`).

## Production Deployment

For Bluehost, build a verified full theme zip before uploading:

```powershell
npm run package:theme
```

Then upload `deploy/hands-and-vision-theme-folder.zip` through Bluehost File
Manager. See [docs/BLUEHOST-DEPLOYMENT.md](docs/BLUEHOST-DEPLOYMENT.md).

Avoid WinSCP Synchronize/Mirror for this theme. It can leave the production theme
folder partially overwritten if the local panel points at a subdirectory such as
`inc`.

## Linting (WordPress PHP)

PHP linting uses [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [WordPress-Coding-Standards](https://github.com/WordPress/WordPress-Coding-Standards). Requires PHP and Composer.

- **Install**: `composer install` (from theme root). On Windows if you get file-lock errors, delete `vendor` and run **`composer update --prefer-source`** instead (clones from git instead of extracting zips).
- **Lint**: `composer lint` or `npm run lint`.
- **Auto-fix**: `composer lint:fix` or `npm run lint:fix`.

Config: `phpcs.xml.dist`. Third-party libs under `inc/lib/bsf-analytics` and `inc/lib/nps-survey` are excluded.

## Design System

CSS source of truth and cascade order:

1. **`assets/css/hv-unified.css`** — All design tokens (`:root`), layout, components, pages, RTL
2. **`assets/css/hv-design-refinements.css`** — Client polish loaded after unified (hero, mobile menu, service tiles)
3. **`assets/css/hv-store-premium.css`** — WooCommerce-only extensions

### Tokens

All `--hv-*` custom properties are defined once at the top of `hv-unified.css`: colors, typography, spacing, radius, shadows, motion.

Key spacing tokens:
- `--hv-space-1` … `--hv-space-11` — 4px-based scale
- `--hv-section-padding-y` — vertical section rhythm (100px)
- `--hv-section-padding-x` — horizontal section padding

### Buttons

| Class | Use |
|-------|-----|
| `hv-btn--cta` | Main call-to-action (lilac gradient) |
| `hv-btn--primary-gold` | Legacy alias for `hv-btn--cta` |
| `hv-btn--primary` | Solid petrol — form submits, strong actions |
| `hv-btn--outline` | Secondary bordered |
| `hv-btn--outline-light` | On dark/video backgrounds |
| `hv-btn--ghost` | Text-only with underline animation |
| `hv-btn--glass` | Frosted hero secondary action |

`--hv-primary` (deep red) is reserved for sales badges, errors, and destructive states — not general CTAs.

### RTL

Use `[dir="rtl"]` overrides and logical properties (`inset-inline`, `margin-inline`). PHP strings: `$is_hebrew ? 'HE' : 'EN'`.

## Multilingual (Hebrew + English)

**Recommended stack:** [Polylang](https://wordpress.org/plugins/polylang/) + **Yoast SEO** (already active on production).

| Piece | Role |
|-------|------|
| Polylang | Language URLs (`/en/…`), content translations, locale switching, RTL/LTR |
| Yoast SEO | Per-language titles, meta, sitemaps; hreflang with Polylang |
| Theme (`inc/accessibility/`) | `handandvision_is_hebrew()`, header switcher, `is_rtl` sync |

**Local setup:** Polylang is installed by `npm run dev`. Run `npm run dev:polylang` to add Hebrew (default, no URL prefix) and English (`/en/` prefix).

**Content workflow:**
1. In WP Admin → Languages, confirm Hebrew + English.
2. For each page/CPT post, create a translation and link them in the Polylang column.
3. Register menus per language (Appearance → Menus → language tabs).
4. Products: ACF `product_title_en` still works for EN titles until you add **Polylang for WooCommerce** (paid) for full shop sync (cart, checkout pages, categories).

**Production:** Install Polylang from Plugins → Add New, then Languages → setup wizard (same settings: Hebrew default, directory name, hide default language). Theme integration in `inc/accessibility/polylang-integration.php` registers `artist`, `service`, `gallery_item`, and `product` for translation.

**SEO best practices (already configured):**
- Canonical language URLs (not `?lang=en` query args)
- `hreflang` via Polylang + Yoast
- `x-default` → Hebrew (default language)
- Per-language sitemaps in Yoast

The legacy `?lang=en` cookie switcher is disabled when Polylang is active.

## SEO

Built-in SEO lives in `inc/seo/` and runs automatically when no major SEO plugin (Yoast, Rank Math, AIOSEO) is active:

| Module | Purpose |
|--------|---------|
| `class-hv-seo.php` | Meta description, Open Graph, Twitter Cards, hreflang (HE/EN), titles, robots |
| `class-hv-seo-schema.php` | JSON-LD: Organization, ArtGallery, WebSite, Person, Service, VisualArtwork, enhanced Product |
| `class-hv-seo-sitemap.php` | WordPress sitemap tuning, robots.txt rules for cart/checkout/search |

Descriptions are pulled from ACF fields, excerpts, and WooCommerce short descriptions. Utility pages (cart, checkout, account, search, 404) are set to `noindex`.
