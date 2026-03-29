<?php
/**
 * Register Gutenberg Blocks for Porfolio MDQ
 */
class MDQ_Block_Registry {

    public function __construct() {
        add_action( 'init', array( $this, 'register_blocks' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
    }

    /**
     * Enqueue Editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'mdq-block-script',
            MDQ_PLUGIN_URL . 'assets/js/mdq-block.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-data', 'wp-i18n' ),
            '1.0.0',
            true
        );
    }

    /**
     * Register Block Type
     */
    public function register_blocks() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type( 'mdq/portfolio', array(
            'render_callback' => array( $this, 'render_mdq_portfolio_block' ),
            'attributes'      => array(
                'limit' => array(
                    'type'    => 'number',
                    'default' => -1,
                ),
                'category' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'language' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'title' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
                'subtitle' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
            ),
        ) );
    }

    /**
     * Callback to render the block
     */
    public function render_mdq_portfolio_block( $attributes ) {
        // We reuse the existing shortcode logic to keep it DRY
        if ( ! class_exists( 'MDQ_Shortcode' ) ) {
            return '<p>Error: MDQ_Shortcode class not found.</p>';
        }

        $shortcode = new MDQ_Shortcode();
        
        // Convert attributes for the shortcode handler
        $params = array(
            'limit'    => $attributes['limit'],
            'category' => $attributes['category'],
            'language' => $attributes['language'],
            'title'    => isset($attributes['title']) ? $attributes['title'] : '',
            'subtitle' => isset($attributes['subtitle']) ? $attributes['subtitle'] : '',
        );

        return $shortcode->render_portfolio( $params );
    }
}

new MDQ_Block_Registry();
