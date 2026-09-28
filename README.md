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

## Releasing

Suite kit: https://github.com/nytafar/kaupang-docs/tree/main/kit. The header `Version:` is the only version source; the
pre-commit hook syncs `FAQ_SCHEMA_BLOCK_VERSION` and regenerates readme.txt (never edit it). Add lines under
`## [Unreleased]` in CHANGELOG.md, then release with `sudo -u myrvann tools/release patch|minor|major [--push]`.

## License
GPL-2.0-or-later
