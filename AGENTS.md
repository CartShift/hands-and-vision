# Repository Guidelines

## Project Structure & Module Organization

This is a custom WordPress theme. Root PHP files are templates (`front-page.php`, `single-artist.php`, `archive-product.php`). Put reusable PHP in `inc/`, view fragments in `template-parts/`, and WooCommerce overrides in `woocommerce/`. CSS, JavaScript, fonts, and images live under `assets/`; `assets/css/hv-unified.css` owns design tokens and base styles. Local scripts are in `docker/`, documentation in `docs/`, and translations in `languages/`. Avoid editing vendored `inc/lib/` or generated/minified assets unless their source is also updated.

## Build, Test, and Development Commands

- `npm install` and `composer install`: install JavaScript and PHP tools.
- `npm run dev`: start Docker services, initialize WordPress, and serve the site at `http://127.0.0.1:8888`.
- `npm run dev:stop`: stop local containers; `npm run dev:destroy` also removes database volumes.
- `npm run dev:logs`: follow WordPress container logs.
- `npm run dev:cli -- user list`: run a WP-CLI command.
- `composer lint` (or `npm run lint`): check PHP against `phpcs.xml.dist`.
- `composer lint:fix`: apply safe PHP_CodeSniffer fixes.

`npm test` is currently a placeholder and intentionally fails; do not treat it as validation.

## Coding Style & Naming Conventions

Follow WordPress Coding Standards. Use four spaces in project PHP, tabs in existing JavaScript, and kebab-case filenames such as `gallery-helpers.php`. Prefix functions with `handandvision_` and classes with `HV_`. Escape output (`esc_html()`, `esc_url()`, `esc_attr()`), sanitize input, and verify nonces. Prefer vanilla JavaScript. Use `--hv-*` CSS tokens, logical properties, and explicit RTL/Hebrew handling.

## Testing Guidelines

There is no automated test framework or coverage threshold. Before a PR, run the PHP linter and exercise affected templates in Docker. Check mobile layouts, keyboard interaction, container logs, relevant WooCommerce flows, and Hebrew RTL plus English LTR rendering. Document scenarios tested.

## Commit & Pull Request Guidelines

Recent history contains mostly placeholder commit messages, so it does not establish a useful convention. Use short, imperative summaries instead, for example `Fix RTL spacing in artist cards`. Keep commits focused. PRs should explain the user-facing change, list validation performed, link any issue, and include before/after screenshots for visual work. Call out template overrides, database assumptions, or deployment/configuration changes.

## Security & Configuration

Never commit credentials, production exports, `.wpress` backups, or log files. Keep deployment values in GitHub secrets and local configuration outside tracked theme code. Treat `npm run dev:destroy` as destructive because it deletes the local database volume.
