# Changelog

All notable changes to this plugin are documented here. Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Security
- FAQPage JSON-LD is encoded with `JSON_HEX_TAG | JSON_HEX_AMP`, so answer text containing `</script>` (e.g. typed as `&lt;/script&gt;`) can no longer close the script tag (stored XSS). Regression check: `tests/check.php` (`wp eval-file`).

### Changed
- Adopted suite kit v2: `FAQ_SCHEMA_BLOCK_VERSION` constant synced from the header, generated readme.txt, `tools/release`; standard header (GitHub Plugin URI, License GPL-2.0-or-later, Requires at least 6.9, Requires PHP 7.2) and a Description of what the plugin is now.

## [1.1.0] - 2026-05-21

### Changed
- Rewritten as a `core/accordion` block variation ("FAQ Schema Accordion", marker class `faq-schema-accordion`) instead of a custom block; FAQPage JSON-LD is built from its accordion items. Requires WordPress 6.9.
- Core's accordion stylesheet is dequeued on pages with the FAQ variation (the theme styles it); other accordions keep it.

### Removed
- The custom block's webpack build and npm package files.

## [1.0.1] - 2025-06-02

### Added
- Smart schema detection for WooCommerce products, with product schema relationships.

### Changed
- Improved accordion animation performance.
- Removed unnecessary styling.

### Fixed
- Schema is output in the head.

## [1.0.0] - 2025-06-02

### Added
- Initial release.
