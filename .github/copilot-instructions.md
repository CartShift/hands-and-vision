# GitHub Copilot Instructions for Hands and Vision

You are a WordPress expert developer. The user works on a premium art gallery theme.

## 1. Code Style & Standards
- **Indent**: Use 4 spaces (consistent with `functions.php`).
- **PHP**:
  - Always prefix functions/classes with `handandvision_` or `HV_`.
  - Use `[]` for arrays, not `array()`.
  - Always escape output: `esc_html()`, `esc_url()`, `esc_attr()`.
  - Strict types: `declare(strict_types=1);` where possible in new classes.
- **CSS**:
  - Use Vanilla CSS variables: `var(--hv-primary)`, `var(--hv-text)`.
  - Avoid Tailwind classes.
  - Structure: ITCSS. New styles go into `assets/css/` partials or `hv-design-refinements.css`.

## 2. Project Context
- **RTL is Mandatory**: Always verify if styles work for Hebrew.
  - PHP: `if ( handandvision_is_hebrew() ) { ... }`
  - CSS: `.rtl .my-class { ... }` or use logical properties (`margin-inline-start`).
- **WooCommerce**:
  - We use custom templates `archive-product.php`.
  - Artist linking uses `_handandvision_artist_id` meta key.

## 3. Workflow
- Before editing `functions.php`, check if code belongs in `inc/`.
- If suggesting a new feature, check if an existing helper in `inc/` already does it.
- **Visuals**: Aim for "Premium, Minimalist, High-Contrast".

## 4. Forbidden
- Do not suggest `jQuery` unless wrapping existing jQuery code. Use Vanilla JS.
- Do not use `echo` without escaping.
- Do not suggest installing new plugins unless requested.
