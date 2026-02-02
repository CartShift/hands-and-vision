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

## Linting (WordPress PHP)

PHP linting uses [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [WordPress-Coding-Standards](https://github.com/WordPress/WordPress-Coding-Standards). Requires PHP and Composer.

- **Install**: `composer install` (from theme root). On Windows if you get file-lock errors, delete `vendor` and run **`composer update --prefer-source`** instead (clones from git instead of extracting zips).
- **Lint**: `composer lint` or `npm run lint`.
- **Auto-fix**: `composer lint:fix` or `npm run lint:fix`.

Config: `phpcs.xml.dist`. Third-party libs under `inc/lib/bsf-analytics` and `inc/lib/nps-survey` are excluded.
