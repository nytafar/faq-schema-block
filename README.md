# FAQ Schema Block

## Description

A variation of WordPress core's Accordion block (`core/accordion`, WordPress 6.9+). Insert "FAQ Schema Accordion" (or
transform an existing accordion): each accordion item's heading is a question and its panel is the answer.

On singular pages that contain it, the plugin outputs one schema.org `FAQPage` JSON-LD script in `<head>`, collected from
every FAQ accordion on the page. The markup is core's own; the plugin only adds the marker class `faq-schema-accordion`,
and dequeues core's accordion stylesheet on those pages so the theme styles them.

## Installation

1. Upload to `/wp-content/plugins/faq-schema-block/`
2. Activate through WordPress plugins screen
3. Use in any post, page, or WooCommerce product

## Technical Details

### Schema Implementation
The plugin implements two types of schema relationships:

1. **Product Pages**:
```json
{
  "@type": "Question",
  "mainEntityOfPage": {
    "@type": "Product",
    "@id": "[product-url]#product"
  }
}
```

2. **Regular Pages**:
```json
{
  "@type": "Question",
  "isPartOf": {
    "@type": "WebPage",
    "@id": "[page-url]#webpage"
  }
}
```

## Load map

One file, no options, no front-end assets of its own.

| Context | Files | Assets | Autoloaded options |
|---|---|---|---|
| Any guest page | main file (1) | none. On a singular post whose content has the FAQ variation it prints the FAQPage JSON-LD in `<head>` and dequeues core's `wp-block-accordion` style | none |
| Post editor (screen base `post`, any post type) | main file | `faq-schema-block-editor` script + style (registers the variation) | none |
| Site editor, widgets, other admin screens | main file | none: the JSON-LD is built from post content only, so the variation isn't offered in templates | none |

No budget file: the default (2 files, no assets) holds on every page type. Check (read-only, exits non-zero on failure):
`gp wp <site> eval-file wp-content/plugins/faq-schema-block/tests/check.php`.

## Releasing

Suite kit: https://github.com/nytafar/kaupang-docs/tree/main/kit. The header `Version:` is the only version source; the
pre-commit hook syncs `FAQ_SCHEMA_BLOCK_VERSION` and regenerates readme.txt (never edit it). Add lines under
`## [Unreleased]` in CHANGELOG.md, then release with `sudo -u myrvann tools/release patch|minor|major [--push]`.

## License
GPL-2.0-or-later
