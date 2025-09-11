<?php
/**
 * Plugin Name: FAQ Schema Block
 * Plugin URI: https://jellum.net/
 * Description: A simple and clean Gutenberg Block for FAQ with proper schema.org implementation.
 * Version: 1.0.1
 * Author: Lasse Jellum
 * Author URI: https://jellum.net/
 * Text Domain: faq-schema-block
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package FAQ_Schema_Block
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main FAQ Schema Block Class
 */
class FAQ_Schema_Block {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance;



	/**
	 * Whether the current page has FAQ blocks.
	 *
	 * @var bool
	 */
	private $has_faq_block = false;

	/**
	 * Get an instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'wp_head', array( $this, 'output_schema' ), 100 );
		add_filter( 'render_block', array( $this, 'process_faq_block' ), 10, 2 );
		add_action( 'wp_footer', array( $this, 'maybe_add_script' ) );
		// Reset FAQ items on each page load
		add_action( 'wp', array( $this, 'reset_faq_items' ) );
	}

	/**
	 * Register the FAQ block.
	 */
	public function register_block() {
		// Register block script.
		wp_register_script(
			'faq-schema-block-editor',
			plugins_url( 'block/dist/index.js', __FILE__ ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'block/dist/index.js' ),
			true
		);

		// Register editor styles.
		wp_register_style(
			'faq-schema-block-editor-style',
			plugins_url( 'block/dist/editor.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'block/dist/editor.css' )
		);

		// Register block.
		register_block_type(
			'faq-schema-block/faq',
			array(
				'editor_script' => 'faq-schema-block-editor',
				'editor_style'  => 'faq-schema-block-editor-style',
				'render_callback' => array( $this, 'render_block' ),
				'attributes' => array(
					'question' => array(
						'type' => 'string',
						'default' => ''
					),
					'answer' => array(
						'type' => 'string',
						'default' => ''
					),
					'id' => array(
						'type' => 'string',
						'default' => ''
					)
				)
			)
		);

		// Set translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'faq-schema-block-editor', 'faq-schema-block' );
		}
	}

    /**
     * Process FAQ block to collect schema data.
     *
     * @param string $block_content The block content.
     * @param array  $block The block data.
     * @return string The block content.
     */
    public function process_faq_block( $block_content, $block ) {
        return $block_content;
    }

    /**
    * Render the FAQ block.
    *
    * @param array $attributes The block attributes.
    * @return string The block HTML.
    */
    public function render_block( $attributes ) {
        error_log('Render block called with attributes: ' . print_r($attributes, true));
        // Get the saved content from attributes
        $question = isset( $attributes['question'] ) ? $attributes['question'] : '';
        $answer = isset( $attributes['answer'] ) ? $attributes['answer'] : '';


        $id = ! empty( $attributes['id'] ) ? $attributes['id'] : 'faq-' . uniqid();

        // Inline styles
        $styles = '
        .faq-schema-item {
            margin-bottom: 1em;
        }
        .faq-schema-item input[type="checkbox"] {
            display: none;
        }
        .faq-schema-question {
            display: block;
            margin: 0;
            padding: 0.5em 2em 0.5em 0;
            cursor: pointer;
            position: relative;
            font-size: 1.1em;
        }
        .faq-schema-question:after {
            content: "+";
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5em;
            line-height: 1;
            color: #666;
            transition: transform 0.2s ease;
        }
        .faq-schema-item input[type="checkbox"]:checked ~ .faq-schema-question:after {
            content: "−";
        }
        .faq-schema-answer {
            height: 0;
            overflow: hidden;
            transition: height 0.25s ease-in-out;
            line-height: 1.6;
            box-sizing: border-box;
            padding-top: 0;
        }
        .faq-schema-item input[type="checkbox"]:checked ~ .faq-schema-answer {
            height: var(--answer-height, auto);
        }';

        // Mark that we have an FAQ block
        $this->has_faq_block = true;

        // Output HTML
        $output = '<div class="faq-schema-item">';
        $output .= '<style>' . $styles . '</style>';
        $output .= '<input type="checkbox" id="' . esc_attr( $id ) . '" class="faq-schema-toggle" />';
        $output .= '<label for="' . esc_attr( $id ) . '" class="faq-schema-question">' . wp_kses_post( $question ) . '</label>';
        $output .= '<div class="faq-schema-answer">' . do_blocks( $answer ) . '</div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Add the height calculation script if FAQ blocks are present.
     */
    public function maybe_add_script() {
        if (!$this->has_faq_block) {
            return;
        }

        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.faq-schema-answer').forEach(answer => {
                const height = answer.scrollHeight;
                answer.style.setProperty('--answer-height', height + 'px');
            });
        });
        </script>
        <?php
    }

    /**
     * Reset FAQ items array on each page load.
     */
    public function reset_faq_items() {
        $this->faq_items = array();
        $this->has_faq_block = false;
    }

    /**
     * Output schema.org JSON-LD in head.
     */
    private function parse_blocks($blocks = array()) {
        global $post;
        $block_data = array();
        
        if (!$post) {
            return $block_data;
        }
        
        if (empty($blocks)) {
            $blocks = isset($post->post_content) ? parse_blocks($post->post_content) : array();
        }
        
        if (!empty($blocks)) {
            foreach ($blocks as $block) {
                if ('faq-schema-block/faq' === $block['blockName']) {
                    $block_data[] = $block;
                    continue;
                }
                // Search in nested blocks
                if (!empty($block['innerBlocks'])) {
                    $block_data = array_merge($block_data, $this->parse_blocks($block['innerBlocks']));
                }
            }
        }
        return $block_data;
    }

    public function output_schema() {
        if (!is_singular()) {
            return;
        }

        $faq_blocks = $this->parse_blocks();
        if (empty($faq_blocks)) {
            return;
        }

        // Build schema data
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'FAQPage',
            'mainEntity' => array()
        );

        foreach ($faq_blocks as $index => $block) {
            if (!empty($block['attrs']['question']) && !empty($block['attrs']['answer'])) {
                $faq_item = array(
                    '@type'          => 'Question',
                    '@id'            => get_permalink() . '#faq-' . ($index + 1),
                    'name'           => wp_strip_all_tags($block['attrs']['question']),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags($block['attrs']['answer'])
                    )
                );

                // Add product reference only on WooCommerce product pages
                if (function_exists('is_product') && is_product()) {
                    $faq_item['mainEntityOfPage'] = array(
                        '@type' => 'Product',
                        '@id'   => get_permalink() . '#product'
                    );
                } else {
                    $faq_item['isPartOf'] = array(
                        '@type' => 'WebPage',
                        '@id'   => get_permalink() . '#webpage'
                    );
                }

                $schema['mainEntity'][] = $faq_item;
            }
        }

        if (!empty($schema['mainEntity'])) {
            echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
        }
	}
}

// Initialize the plugin.
function faq_schema_block_init() {
	FAQ_Schema_Block::get_instance();
}
add_action( 'plugins_loaded', 'faq_schema_block_init' );
