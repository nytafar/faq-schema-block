<?php
/**
 * Plugin Name: FAQ Schema Block
 * Plugin URI: https://jellum.net/
 * Description: A simple and clean Gutenberg Block for FAQ with proper schema.org implementation.
 * Version: 1.1.0
 * Author: Lasse Jellum
 * Author URI: https://jellum.net/
 * Text Domain: faq-schema-block
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package FAQ_Schema_Block
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main FAQ Schema Block Class
 */
class FAQ_Schema_Block
{

    /**
     * Instance of this class.
     *
     * @var object
     */
    private static $instance;



    /**
     * Get an instance of this class.
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        add_action('wp_enqueue_scripts', array($this, 'maybe_dequeue_core_accordion_style'), 20);
        add_action('wp_head', array($this, 'output_schema'), 100);
    }

    /**
     * Dequeue WordPress core's accordion block stylesheet on pages that contain
     * the FAQ Schema variation, since the active theme already styles those
     * selectors. Other accordion blocks on the site keep core's CSS.
     */
    public function maybe_dequeue_core_accordion_style()
    {
        if (!$this->current_post_has_faq_accordion()) {
            return;
        }

        wp_dequeue_style('wp-block-accordion');
    }

    /**
     * Cheap check: does the current singular post contain the FAQ variation?
     * Uses a string search before parsing blocks so non-FAQ pages pay almost
     * nothing.
     */
    private function current_post_has_faq_accordion()
    {
        if (!is_singular()) {
            return false;
        }

        $post = get_post();

        if (!$post || empty($post->post_content)) {
            return false;
        }

        return false !== strpos($post->post_content, 'faq-schema-accordion');
    }

    /**
     * Register the FAQ block.
     */
    public function enqueue_editor_assets()
    {
        $script_path = plugin_dir_path(__FILE__) . 'block/src/index.js';
        $script_url = plugins_url('block/src/index.js', __FILE__);

        if (!file_exists($script_path)) {
            return;
        }

        wp_enqueue_script(
            'faq-schema-block-editor',
            $script_url,
            array('wp-blocks', 'wp-dom-ready', 'wp-i18n'),
            filemtime($script_path),
            true
        );

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('faq-schema-block-editor', 'faq-schema-block');
        }

        $style_path = plugin_dir_path(__FILE__) . 'block/css/editor.css';
        $style_url = plugins_url('block/css/editor.css', __FILE__);

        if (file_exists($style_path)) {
            wp_enqueue_style(
                'faq-schema-block-editor',
                $style_url,
                array(),
                filemtime($style_path)
            );
        }
    }

    private function collect_faq_accordions($blocks)
    {
        $accordions = array();

        foreach ($blocks as $block) {
            if ($this->is_faq_accordion($block)) {
                $accordions[] = $block;
            }

            if (!empty($block['innerBlocks'])) {
                $accordions = array_merge($accordions, $this->collect_faq_accordions($block['innerBlocks']));
            }
        }

        return $accordions;
    }

    private function is_faq_accordion($block)
    {
        if (!isset($block['blockName']) || 'core/accordion' !== $block['blockName']) {
            return false;
        }

        return $this->block_has_class($block, 'faq-schema-accordion');
    }

    private function block_has_class($block, $class_name)
    {
        $attrs_class_name = isset($block['attrs']['className']) ? $block['attrs']['className'] : '';

        if ($this->class_list_contains($attrs_class_name, $class_name)) {
            return true;
        }

        $html = isset($block['innerHTML']) ? $block['innerHTML'] : '';

        if ('' === $html) {
            return false;
        }

        return (bool) preg_match('/class=(["\'])(?=[^"\']*\b' . preg_quote($class_name, '/') . '\b)[^"\']*\1/i', $html);
    }

    private function class_list_contains($class_list, $class_name)
    {
        return in_array($class_name, preg_split('/\s+/', trim($class_list)), true);
    }

    private function extract_faq_items($accordion_block)
    {
        $items = array();

        if (empty($accordion_block['innerBlocks'])) {
            return $items;
        }

        foreach ($accordion_block['innerBlocks'] as $item_block) {
            if (!isset($item_block['blockName']) || 'core/accordion-item' !== $item_block['blockName']) {
                continue;
            }

            $heading_block = $this->find_child_block($item_block, 'core/accordion-heading');
            $panel_block = $this->find_child_block($item_block, 'core/accordion-panel');

            if (!$heading_block || !$panel_block) {
                continue;
            }

            $question = $this->extract_heading_text($heading_block);
            $answer = $this->extract_panel_text($panel_block);

            if ('' === $question || '' === $answer) {
                continue;
            }

            $items[] = array(
                'question' => $question,
                'answer' => $answer,
            );
        }

        return $items;
    }

    private function find_child_block($block, $block_name)
    {
        if (empty($block['innerBlocks'])) {
            return null;
        }

        foreach ($block['innerBlocks'] as $inner_block) {
            if (isset($inner_block['blockName']) && $block_name === $inner_block['blockName']) {
                return $inner_block;
            }
        }

        return null;
    }

    private function extract_heading_text($heading_block)
    {
        if (!empty($heading_block['attrs']['title'])) {
            return $this->normalize_schema_text($heading_block['attrs']['title']);
        }

        $html = isset($heading_block['innerHTML']) ? $heading_block['innerHTML'] : serialize_block($heading_block);
        $title_html = $this->extract_html_by_class($html, 'wp-block-accordion-heading__toggle-title');

        if ('' === $title_html) {
            $title_html = $html;
        }

        return $this->normalize_schema_text($title_html);
    }

    private function extract_panel_text($panel_block)
    {
        $html = '';

        if (!empty($panel_block['innerBlocks'])) {
            foreach ($panel_block['innerBlocks'] as $inner_block) {
                $html .= render_block($inner_block);
            }
        } elseif (!empty($panel_block['innerHTML'])) {
            $html = $panel_block['innerHTML'];
        }

        return $this->normalize_schema_text($html);
    }

    private function extract_html_by_class($html, $class_name)
    {
        $pattern = '/<([a-z0-9]+)\b[^>]*class=(["\'])(?=[^"\']*\b' . preg_quote($class_name, '/') . '\b)[^"\']*\2[^>]*>(.*?)<\/\1>/is';

        if (!preg_match($pattern, $html, $matches)) {
            return '';
        }

        return $matches[3];
    }

    private function normalize_schema_text($html)
    {
        $text = wp_strip_all_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, get_bloginfo('charset'));
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    public function output_schema()
    {
        if (!$this->current_post_has_faq_accordion()) {
            return;
        }

        $post = get_post();
        $blocks = parse_blocks($post->post_content);
        $faq_accordions = $this->collect_faq_accordions($blocks);

        if (empty($faq_accordions)) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );

        $items = array();

        foreach ($faq_accordions as $accordion) {
            $items = array_merge($items, $this->extract_faq_items($accordion));
        }

        foreach ($items as $index => $item) {
                $faq_item = array(
                    '@type' => 'Question',
                    '@id' => get_permalink() . '#faq-' . ($index + 1),
                    'name' => $item['question'],
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => $item['answer']
                    )
                );

                if (function_exists('is_product') && is_product()) {
                    $faq_item['mainEntityOfPage'] = array(
                        '@type' => 'Product',
                        '@id' => get_permalink() . '#product'
                    );
                } else {
                    $faq_item['isPartOf'] = array(
                        '@type' => 'WebPage',
                        '@id' => get_permalink() . '#webpage'
                    );
                }

                $schema['mainEntity'][] = $faq_item;
        }

        if (!empty($schema['mainEntity'])) {
            echo '<script type="application/ld+json" id="faq-schema-block-json-ld">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>' . "\n";
        }
    }
}

// Initialize the plugin.
function faq_schema_block_init()
{
    FAQ_Schema_Block::get_instance();
}
add_action('plugins_loaded', 'faq_schema_block_init');
