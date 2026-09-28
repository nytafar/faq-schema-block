# Changelog

All notable changes to this plugin are documented here. Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Security
- FAQPage JSON-LD is encoded with `JSON_HEX_TAG | JSON_HEX_AMP`, so answer text containing `</script>` (e.g. typed as `&lt;/script&gt;`) can no longer close the script tag (stored XSS). Regression check: `tests/check.php` (`wp eval-file`).
