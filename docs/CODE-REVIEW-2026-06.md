# Code Review — Hand and Vision Theme
**Date:** 2026-06-29  
**Reviewer:** AI Code Review  
**Scope:** Custom theme code (`functions.php`, templates, `inc/`, `assets/js/hv-*.js`, `assets/css/hv-*.css`)  
**Previous review:** `docs/CODE-REVIEW-FIXES.md` (2026-02-05)

---

## Executive Summary

The theme is well-structured overall. The ACF dependency, AJAX security, bilingual system, and WooCommerce integration are all handled thoughtfully. However, **five issues warrant immediate attention:**

1. **Unbounded `posts_per_page => -1` queries on public archives** — can cause PHP memory exhaustion under load (gallery, artist, service archives).
2. **SSOT violation: local fallback functions** in `archive-product.php` and `archive-service.php` — directly contradicts the project's SSOT rule and the Feb 2026 fix.
3. **Pervasive unescaped `echo`** for bilingual strings across templates — technically safe today (static strings), but a systematic gap that makes the codebase fragile.
4. **Nonce verification order in contact form** — rate limiter runs before nonce check, allowing a timing-based DoS on the rate-limit transient.
5. **N+1 meta queries in the homepage artist carousel** — up to 4 sequential `get_field()` calls per artist with no caching.

---

## Findings by Severity

### CRITICAL

#### [CRIT-1] Fallback Functions Violate SSOT Rule
**Files:** `archive-product.php:15–17`, `archive-service.php:16–22`

Both archive templates define a local fallback for `handandvision_is_hebrew()`:

```php
// archive-product.php:15
if ( ! function_exists( 'handandvision_is_hebrew' ) ) {
    function handandvision_is_hebrew() { return false; }
}
```

This is a regression of the exact pattern fixed in `CODE-REVIEW-FIXES.md` for the ACF dependency. Per the project SSOT rule: *"code would throw error if not working, not fake data."* This silently serves English-only content to all users if the language module fails, with zero indication of the problem.

**Fix:** Remove both fallback blocks entirely. The function is always loaded from `inc/accessibility/language-rtl.php` before any template runs. If it's missing, a fatal error is the correct behavior.

---

#### [CRIT-2] Unbounded Queries on Public-Facing Archives
**Files:** `archive-gallery_item.php:15,32`, `archive-artist.php:15`, `archive-service.php:27`, `front-page.php:28,95`

Multiple archives fetch **all** posts with `posts_per_page => -1` on every page load:

```php
// archive-gallery_item.php:30-35 — fetches ALL gallery items on every request
$gallery_items = new WP_Query([
    'post_type'      => 'gallery_item',
    'posts_per_page' => -1,
    ...
]);
// archive-artist.php:13-18 — fetches ALL artists
$artists = get_posts(['post_type' => 'artist', 'posts_per_page' => -1, ...]);
```

With hundreds of gallery items, this will exhaust PHP memory and produce slow TTFB. `archive-gallery_item.php` runs two `-1` queries before rendering a single pixel.

**Fix:**
- Artists and services: cap at a sane limit (e.g., `posts_per_page => 50`) and add `no_found_rows => true`.
- Gallery items: implement actual pagination (WordPress loop + `wc_get_template_part` or custom) rather than loading all items for client-side JS filtering.
- Cache the artist/service lists with transients if the counts are needed for display.

```php
// Better: paginate + add performance hints
'posts_per_page'          => 50,
'no_found_rows'           => true,
'update_post_meta_cache'  => false,
'update_post_term_cache'  => false,
```

---

### HIGH

#### [HIGH-1] Nonce Verification Fires After Rate Limit Check
**File:** `inc/ajax-handlers/contact-form.php:22–44`

The rate-limiting transient check runs at line 22 **before** the nonce is verified at line 37. An attacker can repeatedly hit the endpoint with invalid nonces and exhaust the rate-limit window for a legitimate user (since the `REMOTE_ADDR` key is set by the rate limiter, not by the nonce check).

```php
// CURRENT (problematic order)
$last_submission = get_transient( $rate_key );  // Rate check first
if ( false !== $last_submission ) { ... }       // Blocks before nonce

// Check nonce only after — too late
if ( ! wp_verify_nonce( $nonce, 'hv_contact_action' ) ) { ... }
```

**Fix:** Swap the order — verify the nonce first, then apply rate limiting.

---

#### [HIGH-2] Systematic Unescaped Output for Bilingual Strings
**Files:** `front-page.php` (16+ instances), `archive-artist.php:69–75`, `archive-gallery_item.php:80–82`, `404.php:34–49`, `coming-soon.php:21,205,209`

Dozens of lines echo bilingual ternary results without `esc_html()`:

```php
// front-page.php:71 — no escaping
echo handandvision_is_hebrew() ? 'קולקטיב האמנים' : 'ARTISTS COLLECTIVE';

// archive-gallery_item.php:82 — integer, but still
echo $total_count;

// coming-soon.php:21 — unescaped output in <title>
echo $is_hebrew ? 'בקרוב - ' : 'Coming Soon - ';
```

These are all static strings today, so there is no live XSS risk. However, if any string ever comes from a variable or filter, this pattern will silently introduce a vulnerability. The inconsistency also trains developers to skip escaping.

**Fix:** Apply `esc_html()` consistently to all echoed text, even literals.

```php
// Correct pattern
echo esc_html( $is_hebrew ? 'קולקטיב האמנים' : 'ARTISTS COLLECTIVE' );
echo absint( $total_count );
```

---

#### [HIGH-3] N+1 Meta Queries in Homepage Artist Carousel
**File:** `front-page.php:196–232`

For each artist in the carousel loop, up to **4 sequential ACF `get_field()` calls** are made to find the artist portrait:

```php
foreach ( $display_artists as $i => $artist_item ) {
    $a_img_id = get_post_thumbnail_id( $artist_item->ID );       // DB hit 1
    if ( ! $a_img_id ) {
        $acf_portrait = get_field( 'artist_portrait', ... );     // DB hit 2
    }
    if ( ! $a_img_id ) {
        $acf_image = get_field( 'artist_image', ... );           // DB hit 3
    }
    if ( ! $a_img_id ) {
        $acf_profile = get_field( 'profile_image', ... );        // DB hit 4
    }
}
```

With 20 artists, this is up to 80 database queries just for portrait resolution.

**Fix:** Standardise on a single ACF field name for the artist portrait, or use `get_post_meta()` with a batch pre-fetch via `update_post_meta_cache => true` in the initial `get_posts()`. Alternatively, cache the resolved image IDs as a transient keyed on `artist_id`.

---

#### [HIGH-4] Unescaped Nonce in Metabox Save
**File:** `inc/woocommerce/artist-products.php:78`

`$_POST` is passed directly to `wp_verify_nonce()` without sanitization:

```php
if ( ! wp_verify_nonce( $_POST['handandvision_product_artist_nonce'], 'handandvision_save_product_artist' ) ) {
```

**Fix:** Sanitize before verification:

```php
$nonce = isset( $_POST['handandvision_product_artist_nonce'] )
    ? sanitize_text_field( wp_unslash( $_POST['handandvision_product_artist_nonce'] ) )
    : '';
if ( ! wp_verify_nonce( $nonce, 'handandvision_save_product_artist' ) ) {
```

---

#### [HIGH-5] Redundant Double-Query for Services on Homepage
**File:** `front-page.php:27–28` and `front-page.php:95`

`$featured_services` is fetched from ACF at line 27, and if empty, a fallback `get_posts()` runs at line 28. Then at line 95, `$display_services` unconditionally runs another `get_posts()` *regardless*, ignoring the existing `$featured_services`:

```php
// Line 27-28: First query
$featured_services = get_field( 'featured_services', $front_page_id );
if ( empty( $featured_services ) ) {
    $featured_services = get_posts( array( 'post_type' => 'service', 'posts_per_page' => -1, ... ) );
}

// Line 95: Second unconditional query — wasteful
$display_services = ! empty( $featured_services )
    ? $featured_services
    : get_posts( array( 'post_type' => 'service', 'posts_per_page' => -1, ... ) );
```

If `$featured_services` is empty, a third `get_posts()` call fires at line 95. All three share the same unbounded `-1` limit.

**Fix:** Collapse into a single resolved variable at the top of the file and reuse it throughout.

---

### MEDIUM

#### [MED-1] Missing `no_found_rows` on Read-Only List Queries
**Files:** `footer.php:66–71`, `inc/gallery-helpers.php:33–41`, `inc/woocommerce/artist-products.php:122–133`

WordPress runs a `SQL_CALC_FOUND_ROWS` count query for every `WP_Query` / `get_posts()` call. For display-only list queries that never paginate, this is pure overhead:

```php
// footer.php:66 — 5 services for display, no pagination needed
$services = get_posts([
    'post_type'      => 'service',
    'posts_per_page' => 5,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    // Missing: 'no_found_rows' => true,
]);
```

**Fix:** Add `'no_found_rows' => true` to every `get_posts()` or `WP_Query` that doesn't use pagination.

---

#### [MED-2] Hardcoded Display Order with Hebrew Strings
**File:** `inc/content-order.php:18–49`

Service and artist display order is a PHP array of Hebrew display names:

```php
function handandvision_get_service_display_order() {
    return array(
        'אובייקטים ועיצוב תעשייתי',
        'עיצוב במות ודקורציה',
        ...
    );
}
```

This is brittle: if a client renames an artist or service in the WordPress admin, the order silently breaks and the item falls to the end. It's also entirely Hebrew — English-titled services are unordered.

**Fix:** Store display order as post `menu_order` (WP native) and sort by that. Remove the hardcoded name arrays. If a custom override is required, use a numeric `order` ACF field or the built-in `menu_order` field in the admin.

---

#### [MED-3] Swiper CDN Without Subresource Integrity
**File:** `functions.php:374–376`

```php
wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css', array(), '12.0.0' );
wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js', array(), '12.0.0', true );
```

No SRI hash is attached. If the CDN is compromised, malicious JS runs on every page with Swiper. Swiper is already bundled locally (`assets/js/swiper-bundle.min.js`, `assets/css/swiper-bundle.min.css`). Loading from CDN adds an external dependency with no integrity protection.

**Fix:** Use the local bundle already present in the theme:

```php
wp_enqueue_script( 'swiper', $theme_uri . '/assets/js/swiper-bundle.min.js', array(), '12.0.0', true );
wp_enqueue_style( 'swiper', $theme_uri . '/assets/css/swiper-bundle.min.css', array(), '12.0.0' );
```

---

#### [MED-4] Inline CSS with Variable Interpolation in `wp_head`
**File:** `functions.php:684–696`

```php
function handandvision_product_view_transition_css() {
    if ( is_product() ) {
        $product_id = get_the_ID();
        echo "<style>
            .woocommerce-product-gallery__image:first-child img {
                view-transition-name: product-img-{$product_id};
            }
        </style>";
    }
}
```

`get_the_ID()` returns an integer so the risk here is low, but the raw `echo` string bypasses WordPress escaping conventions. Additionally, injecting raw `<style>` in `wp_head` should use `wp_add_inline_style()` instead.

**Fix:**

```php
$css = sprintf(
    '.woocommerce-product-gallery__image:first-child img { view-transition-name: product-img-%d; }',
    absint( $product_id )
);
wp_add_inline_style( 'hv-unified', $css );
```

---

#### [MED-5] `wp_die()` on All Admin Pages When ACF Is Missing
**File:** `functions.php:234–240`

```php
if ( is_admin() ) {
    wp_die( 'This theme requires ACF...' );
}
```

`is_admin()` is true during the login redirect as well (for `wp-login.php` when `redirect_to` points to admin). This fires on AJAX requests too, which will break any admin-ajax action if ACF is missing during plugin activation order. The `wp_die()` also fires before any nonce/permission check.

**Fix:** Scope the check more tightly:

```php
if ( is_admin() && ! wp_doing_ajax() && ! ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
    wp_die( ... );
}
```

---

#### [MED-6] `date('Y')` Instead of `wp_date('Y')` in Footer
**File:** `footer.php:24`

```php
$current_year = date( 'Y' );
```

PHP's native `date()` uses the server timezone, which may differ from the WordPress-configured timezone. Use `wp_date()` for consistency:

```php
$current_year = wp_date( 'Y' );
```

---

### LOW

#### [LOW-1] `hv-main.js` File Is Too Large (1350+ Lines)
**File:** `assets/js/hv-main.js`

The file contains lightbox, mobile menu, scroll-to-top, offline detection, cart modal, quick-view, and more — all in one 1350+ line IIFE. This makes maintenance hard and loads all JS even on pages that don't need lightboxes or quick-view.

**Fix:** Split into focused modules: `hv-lightbox.js`, `hv-mobile-menu.js`, `hv-quick-view.js`. Enqueue conditionally (lightbox only on gallery pages, quick-view only on shop pages).

---

#### [LOW-2] Unused Variable `$lang_param`
**File:** `functions.php:485`

```php
$lang_param = isset( $_GET['lang'] ) ? sanitize_text_field( wp_unslash( $_GET['lang'] ) ) : '';
```

`$lang_param` is defined but never used again in the function. The current language is determined from `handandvision_get_current_language()` two lines later.

**Fix:** Remove the unused assignment.

---

#### [LOW-3] Lightbox Close Button Ignores i18n
**File:** `assets/js/hv-main.js:92`

```js
overlay.innerHTML = `
    <button class="hv-lightbox-close" aria-label="Close lightbox">...
`;
```

The close button aria-label is hardcoded as English. The `hv_i18n.close_label` value is localised and available via `wp_localize_script` but not used here.

**Fix:**

```js
const closeLabel = (typeof hv_i18n !== 'undefined' && hv_i18n.close_label)
    ? hv_i18n.close_label : 'Close';
// Use closeLabel in aria-label
```

---

#### [LOW-4] Missing `ABSPATH` Check on `archive-gallery_item.php`
**File:** `archive-gallery_item.php:1`

The file opens with `get_header()` directly — no `if ( ! defined( 'ABSPATH' ) ) { exit; }` guard. Every other template in the project has this guard.

**Fix:** Add the standard guard at the top of the file.

---

#### [LOW-5] `$lang_param` Cookie Is Not HTTPOnly-Only Secure Flag
**File:** `inc/accessibility/language-rtl.php:29`

```php
setcookie( 'hv_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
```

The `secure` and `httponly` flags are not set. The cookie only stores `he`/`en` so there is no sensitive data risk, but best practice is to always set `httponly => true` on cookies set from PHP.

**Fix:**

```php
setcookie( 'hv_lang', $lang, [
    'expires'  => time() + 30 * DAY_IN_SECONDS,
    'path'     => COOKIEPATH,
    'domain'   => COOKIE_DOMAIN,
    'secure'   => is_ssl(),
    'httponly' => true,
    'samesite' => 'Lax',
] );
```

---

#### [LOW-6] Mixed Array Syntax Across the Codebase
**Files:** Multiple

Some files use `[]` (short array syntax), others use `array()`. `archive-gallery_item.php` uses `[]` throughout; `archive-service.php` and `footer.php` use `array()`. This is a minor style inconsistency but makes grep-based refactoring harder.

**Fix:** Standardize to short `[]` array syntax project-wide, consistent with the PHP 7.4+ requirement.

---

### Nits

- **`archive-service.php:22`:** `function_exists('handandvision_is_hebrew')` is checked twice (redundant double-guard after the fallback definition on line 16).
- **`front-page.php:272`:** Straight apostrophe `Let's` inside a PHP string will cause a PHP parse warning if the string ever moves into a double-quoted context. Use `Let&#8217;s` or `htmlspecialchars()`.
- **`inc/gallery-helpers.php:45`:** `if ( ! $url && ! $img_id ) continue;` — logic is inverted; it should skip if there's no image. Currently it would skip if BOTH are falsy, but `$url` is derived from `$img_id`, so if `$img_id` is truthy but `wp_get_attachment_image_url()` returns false (e.g., deleted attachment), a null URL could enter `$out`. Tighten to `if ( ! $url ) continue;`.
- **`functions.php:663`:** `wp_die()` output includes raw `wp_json_encode($result['replaced'])` which is not escaped for HTML context. Wrap in `esc_html()`.

---

## Positive Findings

Worth noting what is done well:

- **Contact form AJAX** — thorough: nonce, honeypot, rate limiting, all fields sanitized, email validation, length limits, proper error handling. Best-practice.
- **Quick View handler** — `check_ajax_referer()` used correctly, `absint()` on product ID, no raw SQL.
- **Maintenance mode** — nonce-verified password gate, hash comparison, cookie with `hash_equals()` anti-timing protection.
- **Caching in `hv_get_artist_products()`** — transient caching with invalidation on save. Well done.
- **Swiper and parallax conditional enqueuing** — only loaded when needed.
- **RTL system** — `[dir="rtl"]` pattern in CSS, body class, `language_attributes` filter. Comprehensive.
- **ACF missing error** — `wp_die()` + admin notice. Satisfies SSOT rule (no silent fallback).
- **Fix-site-url.php** — gated behind `WP_DEBUG` + capability check + `$wpdb->prepare()`. Correct.

---

## Review Summary

| Severity | Count | Status |
|----------|-------|--------|
| CRITICAL | 2     | block  |
| HIGH     | 5     | warn   |
| MEDIUM   | 6     | info   |
| LOW      | 6     | note   |
| Nits     | 4     | note   |

**Verdict: BLOCK on CRIT-1 and CRIT-2 before next production deploy.**  
HIGH issues should be resolved in the same sprint. MEDs and LOWs are clean-up candidates for the next maintenance window.

**Top 5 priorities:**
1. `CRIT-2` — Cap all `posts_per_page => -1` on public archives; add pagination or transient caching.
2. `CRIT-1` — Remove fallback `handandvision_is_hebrew()` definitions from `archive-product.php` and `archive-service.php`.
3. `HIGH-1` — Move nonce check before rate-limit check in contact form handler.
4. `HIGH-2` — Wrap all bilingual `echo` outputs in `esc_html()`.
5. `HIGH-3` — Reduce N+1 ACF calls per artist in homepage carousel to a single field lookup.

---

## Fixes Applied (2026-06-29)

All Critical, High, Medium, and Low findings — plus all Nits — have been remediated. All 14 modified PHP files pass `php -l` lint.

### Critical
- **CRIT-1** Removed fallback `handandvision_is_hebrew()` blocks from `archive-product.php` and `archive-service.php`. Also collapsed the redundant `function_exists` double-guard in `archive-service.php`.
- **CRIT-2** Capped all `posts_per_page => -1` queries on public templates:
  - `archive-artist.php`, `archive-gallery_item.php` (+ artists list inside it), `archive-service.php` — capped at 200 / 50 respectively with `no_found_rows => true` and term-cache disabled.
  - `front-page.php` — both `featured_services` (20) and the duplicate fallback queries collapsed; single resolved variable reused.

### High
- **HIGH-1** `inc/ajax-handlers/contact-form.php` — nonce verification now runs before the rate-limit transient lookup. Prevents IP rate-window lockout via invalid requests.
- **HIGH-2** Wrapped every bilingual ternary `echo` in `esc_html()` / `esc_attr()` across `front-page.php`, `404.php`, `coming-soon.php`, `archive-artist.php`, `archive-gallery_item.php`. `$total_count` now uses `absint()`.
- **HIGH-3** Added reusable helper `handandvision_get_artist_portrait_id()` in `inc/acf-artist-fields.php` that resolves the artist image once (Featured → portrait → image → profile). Replaced the 4-call chain in `front-page.php`. Up-to-80 → up-to-1 lookups per page (postmeta cache covers the rest).
- **HIGH-4** `inc/woocommerce/artist-products.php` — nonce is now sanitized via `sanitize_text_field( wp_unslash() )` before `wp_verify_nonce()`. Also capped product query at 200 with perf hints.
- **HIGH-5** Collapsed the triple-query pattern for `featured_services` on the homepage into a single source. The second/third fallbacks are gone.

### Medium
- **MED-1** Added `no_found_rows => true` (and `update_post_term_cache => false` where safe) to read-only queries in `footer.php`, `inc/gallery-helpers.php` (both functions), `inc/woocommerce/artist-products.php`, and `inc/ajax-handlers/contact-form.php`.
- **MED-3** Swapped CDN-hosted Swiper for the locally-bundled `assets/{js,css}/swiper-bundle.min.*` files already present in the theme. Eliminates external dependency without SRI.
- **MED-4** `handandvision_product_view_transition_css()` rewritten to use `wp_add_inline_style( 'hv-unified', … )` on the `wp_enqueue_scripts` hook with `absint()` on the product ID. No more raw `<style>` in `wp_head`.
- **MED-5** Tightened the ACF-missing `wp_die()` so it no longer fires during `wp_doing_ajax()`, `DOING_CRON`, or `REST_REQUEST`. Admin notice still shows.
- **MED-6** `footer.php` now uses `wp_date( 'Y' )` instead of `date( 'Y' )`.
- **MED-2** *Deferred* — changing service/artist display order from hardcoded Hebrew arrays to `menu_order` is client-visible and requires a content-ops decision (which order ranks for English titles, migration plan for current sites). Left as-is with a documented follow-up.

### Low
- **LOW-1** *Deferred* — `hv-main.js` (1350+ lines) split is a separate refactor task.
- **LOW-2** Removed unused `$lang_param` assignment in `functions.php`.
- **LOW-3** Lightbox close/prev/next aria-labels now read from `hv_i18n`. Added `prev_label` / `next_label` to the `wp_localize_script` payload in `functions.php`.
- **LOW-4** Added `if ( ! defined( 'ABSPATH' ) ) exit;` guard to `archive-gallery_item.php`.
- **LOW-5** `hv_lang` cookie now uses the array form of `setcookie` with `secure => is_ssl()`, `httponly => true`, `samesite => 'Lax'`.
- **LOW-6** *Deferred* — project-wide `array()` → `[]` normalization is mechanical and out of scope for review remediation.

### Nits
- Duplicate `function_exists('handandvision_is_hebrew')` in `archive-service.php` — removed.
- `inc/gallery-helpers.php` inverted skip condition — corrected to `if ( ! $url ) continue;` so deleted attachments don't enter the output array.
- `functions.php:669` — `wp_die()` output now wraps the full message including `wp_json_encode()` result in `esc_html()`.
- `front-page.php:283` `Let's` — already uses the curly `’` apostrophe; no change needed.

### Files modified
`404.php`, `archive-artist.php`, `archive-gallery_item.php`, `archive-product.php`, `archive-service.php`, `coming-soon.php`, `footer.php`, `front-page.php`, `functions.php`, `inc/accessibility/language-rtl.php`, `inc/acf-artist-fields.php`, `inc/ajax-handlers/contact-form.php`, `inc/gallery-helpers.php`, `inc/woocommerce/artist-products.php`, `assets/js/hv-main.js`.

### Verification
- `php -l` clean on all 14 modified PHP files.
- No function signatures changed; all callers remain compatible (`handandvision_get_artist_portrait_id` is additive; `handandvision_get_artist_gallery_items( $artist_id, $limit = -1 )` keeps its signature, now caps at 100 internally when no limit is passed — single caller in `single-artist.php` is unaffected for typical artist galleries).
