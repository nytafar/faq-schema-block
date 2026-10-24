<?php
/**
 * Runnable check: the FAQPage JSON-LD must not let answer text close its <script> tag, it appears only on posts
 * that carry the FAQ variation, and the editor assets load only in post editors (loading Track 5/6).
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

// No JSON-LD where no FAQ accordion is in the content (accordion without the marker class), nor off singular views.
$post = new WP_Post( (object) array( 'ID' => 0, 'post_type' => 'page', 'post_content' => str_replace( 'faq-schema-accordion', 'plain', $content ), 'filter' => 'raw' ) );
ob_start();
FAQ_Schema_Block::get_instance()->output_schema();
'' === ob_get_clean() || $fail( 'JSON-LD on a post without the FAQ variation' );
$post                  = new WP_Post( (object) array( 'ID' => 0, 'post_type' => 'page', 'post_content' => $content, 'filter' => 'raw' ) );
$wp_query->is_singular = false;
ob_start();
FAQ_Schema_Block::get_instance()->output_schema();
'' === ob_get_clean() || $fail( 'JSON-LD on a non-singular view' );
echo "ok  JSON-LD only on singular posts with the FAQ variation\n";

// Admin precision (Pattern E): no admin_enqueue_scripts callbacks at all; editor assets only on post-editor screens.
foreach ( $GLOBALS['wp_filter']['admin_enqueue_scripts']->callbacks ?? array() as $cbs ) {
	foreach ( $cbs as $cb ) {
		if ( is_array( $cb['function'] ) && $cb['function'][0] instanceof FAQ_Schema_Block ) {
			$fail( 'admin_enqueue_scripts callback registered (assets would reach every admin screen)' );
		}
	}
}
require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
require_once ABSPATH . 'wp-admin/includes/screen.php';
$queued = static function () {
	return in_array( 'faq-schema-block-editor', wp_scripts()->queue, true ) || in_array( 'faq-schema-block-editor', wp_styles()->queue, true );
};
foreach ( array( 'site-editor', 'widgets', 'index' ) as $foreign ) {
	set_current_screen( $foreign );
	FAQ_Schema_Block::get_instance()->enqueue_editor_assets();
	$queued() && $fail( "editor assets queued on the {$foreign} screen" );
}
set_current_screen( 'page' );
'post' === get_current_screen()->base || $fail( 'test setup: page screen base is ' . get_current_screen()->base );
FAQ_Schema_Block::get_instance()->enqueue_editor_assets();
$queued() || $fail( 'editor assets missing in the page editor (check is vacuous)' );
echo "ok  editor assets only in post editors (not site-editor, widgets, dashboard)\n";

// Storefront load budget (conventions.md §5B).
if (class_exists('WP_CLI') && is_array(WP_CLI::get_runner()->find_command_to_run(['kaupang-assets', 'audit']))) {
	$audit = WP_CLI::runcommand('kaupang-assets audit --plugins=faq-schema-block --budget', ['return' => 'all', 'exit_error' => false]);
	echo $audit->stdout . "\n";
	$audit->return_code === 0 && $audit->stdout !== '' || $fail('kaupang-assets audit --plugins=faq-schema-block --budget exits 0' . ': ' . trim($audit->stderr));
	echo "ok  kaupang-assets audit --plugins=faq-schema-block --budget exits 0\n";
} else {
	echo "  skip storefront budget: `wp kaupang-assets audit` is not available here\n";
}
