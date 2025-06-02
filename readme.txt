=== FAQ Schema Block ===
Contributors: lassejellum
Tags: faq, schema, structured data, block, gutenberg
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple and clean Gutenberg Block for FAQ with proper schema.org implementation.

== Description ==

FAQ Schema Block provides a simple and efficient way to add FAQs to your WordPress site with proper schema.org implementation for better SEO.

**Key Features:**

* Clean, minimal FAQ block with toggle functionality
* Proper schema.org JSON-LD implementation in the head
* Aggregates all FAQ blocks on the page into a single schema
* Minimal styling using em units for better responsiveness
* No unnecessary block options - just pure functionality

The FAQ Schema Block follows best practices for FAQ schema implementation:
* All FAQ items are aggregated and output as a single schema in the head
* Properly formatted according to Google's structured data guidelines
* Clean, semantic HTML output

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/faq-schema-block` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the FAQ Schema block in any post or page

== Frequently Asked Questions ==

= How does the schema implementation work? =

The plugin collects all FAQ blocks on the page and outputs a single, aggregated schema.org JSON-LD script in the head of the document, following Google's guidelines for FAQ page structured data.

= Is the styling customizable? =

The plugin uses minimal, clean styling with em units for better responsiveness. The styling is applied inline only on pages that use the block, keeping your site lean and fast.

= Does this work with page builders? =

Yes, as long as they support Gutenberg blocks, the FAQ Schema Block will work properly.

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
