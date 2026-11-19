=== FAQ Schema Block ===
Contributors: lassejellum
Tags: faq, schema, structured data, accordion, block
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.1.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A core/accordion block variation that outputs its questions and answers as schema.org FAQPage JSON-LD.

== Description ==

A variation of WordPress core's Accordion block (`core/accordion`, WordPress 6.9+). Insert "FAQ Schema Accordion" (or
transform an existing accordion): each accordion item's heading is a question and its panel is the answer.

On singular pages that contain it, the plugin outputs one schema.org `FAQPage` JSON-LD script in `<head>`, collected from
every FAQ accordion on the page. The markup is core's own; the plugin only adds the marker class `faq-schema-accordion`,
and dequeues core's accordion stylesheet on those pages so the theme styles them.

== Installation ==

1. Upload to `/wp-content/plugins/faq-schema-block/`
2. Activate through WordPress plugins screen
3. Use in any post, page, or WooCommerce product

== Changelog ==

= 1.1.1 – 2026-11-19 =

**Security**
* check.php runs the load budget audit.
* FAQPage JSON-LD is encoded with `JSON_HEX_TAG | JSON_HEX_AMP`, so answer text containing `</script>` (e.g. typed as `&lt;/script&gt;`) can no longer close the script tag (stored XSS). Regression check: `tests/check.php` (`wp eval-file`).

**Changed**
* Editor assets load only in post editors (screen base `post`), no longer in the site editor or widgets screen, where FAQ content would produce no JSON-LD. `tests/check.php` now also proves JSON-LD only on singular posts with the variation and the editor gate. README `## Load map`.
* Adopted suite kit v2: `FAQ_SCHEMA_BLOCK_VERSION` constant synced from the header, generated readme.txt, `tools/release`; standard header (GitHub Plugin URI, License GPL-2.0-or-later, Requires at least 6.9, Requires PHP 7.2) and a Description of what the plugin is now.

= 1.1.0 – 2026-05-21 =

**Changed**
* Rewritten as a `core/accordion` block variation ("FAQ Schema Accordion", marker class `faq-schema-accordion`) instead of a custom block; FAQPage JSON-LD is built from its accordion items. Requires WordPress 6.9.
* Core's accordion stylesheet is dequeued on pages with the FAQ variation (the theme styles it); other accordions keep it.

**Removed**
* The custom block's webpack build and npm package files.

= 1.0.1 – 2025-06-02 =

**Added**
* Smart schema detection for WooCommerce products, with product schema relationships.

**Changed**
* Improved accordion animation performance.
* Removed unnecessary styling.

**Fixed**
* Schema is output in the head.

= 1.0.0 – 2025-06-02 =

**Added**
* Initial release.
