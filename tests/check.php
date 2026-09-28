<?php
/**
 * Runnable check: the FAQPage JSON-LD must not let answer text close its <script> tag.
 *
 *   sudo -u <siteuser> wp --path=<docroot> eval-file wp-content/plugins/faq-schema-block/tests/check.php
 *
 * Exits non-zero on failure. Uses a fake in-memory post; writes nothing.
 */

PHP_SAPI === 'cli' || exit;

$payload = '&lt;/script&gt;&lt;script&gt;alert(1)&lt;/script&gt; &amp; &lt;!-- x';
$content = '<!-- wp:accordion {"className":"faq-schema-accordion"} --><div class="wp-block-accordion faq-schema-accordion">'
	. '<!-- wp:accordion-item --><div class="wp-block-accordion-item">'
	. '<!-- wp:accordion-heading {"title":"Q?"} /-->'
	. '<!-- wp:accordion-panel --><div class="wp-block-accordion-panel">'
	. '<!-- wp:paragraph --><p>' . $payload . '</p><!-- /wp:paragraph -->'
	. '</div><!-- /wp:accordion-panel -->'
	. '</div><!-- /wp:accordion-item -->'
	. '</div><!-- /wp:accordion -->';

global $post, $wp_query;
$post                   = new WP_Post( (object) array( 'ID' => 0, 'post_type' => 'page', 'post_content' => $content, 'filter' => 'raw' ) );
$wp_query->is_singular  = true;

ob_start();
FAQ_Schema_Block::get_instance()->output_schema();
$out = ob_get_clean();

$fail = function ( $msg ) use ( $out ) {
	fwrite( STDERR, "FAIL: {$msg}\n{$out}\n" );
	exit( 1 );
};

if ( ! preg_match( '#^<script type="application/ld\+json"[^>]*>(.*)</script>\s*$#s', $out, $m ) ) {
	$fail( 'no JSON-LD script block emitted' );
}
$body = $m[1];
if ( false !== stripos( $body, '</script' ) || false !== strpos( $body, '<!--' ) ) {
	$fail( 'JSON-LD body contains a literal </script or <!-- (script breakout)' );
}
$data = json_decode( $body, true );
if ( ! is_array( $data ) || false === strpos( (string) ( $data['mainEntity'][0]['acceptedAnswer']['text'] ?? '' ), '</script><script>alert(1)</script>' ) ) {
	$fail( 'answer text did not round-trip through the JSON-LD (check is vacuous)' );
}
echo "ok  faq JSON-LD escapes </script and round-trips the answer\n";
