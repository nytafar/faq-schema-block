=== FAQ Schema Block ===
Contributors: lassejellum
Tags: faq, schema, structured data, block, gutenberg, woocommerce
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A clean, performant FAQ block with smart schema.org implementation, optimized for both regular pages and WooCommerce products.

== Description ==

FAQ Schema Block provides a minimalist yet powerful way to add FAQs to your WordPress site with intelligent schema.org implementation that adapts to your content type.

**Key Features:**

* Smart schema detection - automatically adapts between product and regular page schemas
* Clean, performant accordion functionality with pure CSS
* Proper schema.org JSON-LD implementation in the head section
* WooCommerce integration - links FAQs directly to product schema
* Zero dependencies - no external libraries required
* Minimal styling - adapts to your theme seamlessly

**Technical Features:**

* Direct block parsing for reliable schema generation
* Efficient CSS-only animations for better performance
* Proper schema relationships (mainEntityOfPage for products, isPartOf for regular pages)
* Semantic HTML output with proper ARIA attributes
* Inline styles only on pages that use the block

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/faq-schema-block` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the FAQ Schema block in any post, page, or WooCommerce product

== Frequently Asked Questions ==

= How does the schema implementation work? =

The plugin parses blocks directly from the page content and outputs a single, aggregated schema.org JSON-LD script in the head. On WooCommerce product pages, it automatically links FAQs to the product schema for better SEO integration.

= How does it handle styling? =

The plugin uses minimal, CSS-only styling with proper box-sizing and em units. All animations are CSS-based for optimal performance, and styles are inlined only where needed.

= Does it affect page performance? =

No, the plugin is designed for maximum performance:
* CSS-only animations
* No external dependencies
* Minimal DOM manipulation
* Efficient block parsing
* Inline styles only on relevant pages

== Changelog ==

= 1.0.1 =
* Added smart schema detection for WooCommerce products
* Improved accordion animation performance
* Fixed schema output location to head
* Removed unnecessary styling
* Added proper product schema relationships

= 1.0.0 =
* Initial release

== Screenshots ==

1. The FAQ block in the editor
2. FAQ blocks displayed on the front end
3. The schema.org structured data generated in the head

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release
