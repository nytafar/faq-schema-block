# FAQ Schema Block

A clean, performant FAQ block with smart schema.org implementation, optimized for both regular pages and WooCommerce products.

![Version](https://img.shields.io/badge/version-1.0.1-blue.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D7.0-green.svg)
![WordPress Version](https://img.shields.io/badge/wordpress-6.5%20tested-green.svg)

## Features

### Smart Schema Implementation
- Automatic content-type detection (Product vs Regular pages)
- Proper schema relationships in JSON-LD
- WooCommerce product schema integration
- Clean, semantic HTML output

### Performance Focused
- CSS-only animations
- Zero JavaScript dependencies
- Minimal DOM manipulation
- Efficient block parsing
- Inline styles only where needed

### Developer Friendly
- Direct block parsing API
- Clean, documented code
- Proper schema relationships
- Semantic HTML with ARIA
- Minimal styling footprint

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

### Performance
- CSS-only accordion animations
- No external dependencies
- Minimal DOM operations
- Efficient block parsing
- Inline styles only on relevant pages

## Changelog

### 1.0.1
- Added smart schema detection for WooCommerce products
- Improved accordion animation performance
- Fixed schema output location to head
- Removed unnecessary styling
- Added proper product schema relationships

### 1.0.0
- Initial release

## License
GPLv2 or later
