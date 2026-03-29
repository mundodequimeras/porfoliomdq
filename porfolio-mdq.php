<?php
/**
 * Plugin Name: Porfolio MDQ
 * Plugin URI: https://mundodequimeras.com
 * Description: Un plugin profesional para gestionar el porfolio con categorías, lenguajes e iconos.
 * Version: 1.4.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Tested up to: 6.4
 * Author: Diego Lazo
 * Author URI: https://mundodequimeras.com
 * License: GPL2
 * Text Domain: porfoliomdq
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants
define( 'MDQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MDQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MDQ_VERSION', '1.4.1' );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-mdq-admin-about.php';
new MDQ_Admin_About();

// Custom Post Type and Meta
require_once plugin_dir_path( __FILE__ ) . 'includes/class-mdq-cpt.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-meta.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-project-meta.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-shortcode.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-block-registry.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-settings.php';
require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-updater.php';

// Initialize Updater
new MDQ_Updater( __FILE__, 'mundodequimeras/porfoliomdq' );

/**
 * Register Elementor Widget
 */
function mdq_register_elementor_widgets( $widgets_manager ) {
	require_once MDQ_PLUGIN_DIR . 'includes/class-mdq-elementor-widget.php';
	$widgets_manager->register( new \MDQ_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'mdq_register_elementor_widgets' );

/**
 * Initialize the plugin
 */
function mdq_init_portfolio() {
	// Let the classes handle their own hooks
}
add_action( 'plugins_loaded', 'mdq_init_portfolio' );

/**
 * Enqueue scripts and styles
 */
function mdq_enqueue_assets() {
    // Font Awesome for icons
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' );
    
    // Main Plugin Styles
    wp_enqueue_style( 'mdq-porfolio-style', MDQ_PLUGIN_URL . 'assets/css/style.css', array(), MDQ_VERSION );
    
    // GLightbox for Gallery
    wp_enqueue_style( 'glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css' );
    wp_enqueue_script( 'glightbox-js', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', array(), '1.0.1', true );
    
    // Filtering Logic
    wp_enqueue_script( 'mdq-porfolio-filter', MDQ_PLUGIN_URL . 'assets/js/filter.js', array('jquery'), MDQ_VERSION, true );
    wp_add_inline_script( 'mdq-porfolio-filter', 'const mdqData = ' . json_encode( array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'mdq_portfolio_nonce' ),
    ) ), 'before' );

    // Init GLightbox
    wp_add_inline_script( 'glightbox-js', 'document.addEventListener("DOMContentLoaded", function() { if (typeof GLightbox !== "undefined") { const lightbox = GLightbox({ selector: ".mdq-gallery-link", touchNavigation: true, loop: true, zoomable: true }); } });' );
}
add_action( 'wp_enqueue_scripts', 'mdq_enqueue_assets' );

/**
 * Enqueue Admin Assets
 */
function mdq_enqueue_admin_assets() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'mdq_project' ) {
        return;
    }

    wp_enqueue_media();
    
    // Custom Admin CSS for Icons and Layout
    ob_start();
    ?>
    <style>
        /* Add Icons to Submenus for Porfolio MDQ */
        #menu-posts-mdq_project ul.wp-submenu li a:before {
            font-family: dashicons !important;
            content: "\f10c"; /* Default circle */
            display: inline-block;
            width: 20px;
            height: 20px;
            font-size: 16px;
            line-height: 1.4;
            color: rgba(240,245,250,.6);
            vertical-align: middle;
            margin-right: 8px;
            -webkit-font-smoothing: antialiased;
        }
        #menu-posts-mdq_project ul.wp-submenu li a:hover:before { color: #72aee6; }

        /* Specific Icons Mapping (New Reordered List) */
        /* 1. Dashboard */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(1) a:before { content: "\f170"; } 
        
        /* 2. Todos los Proyectos */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(2) a:before { content: "\f105"; } 
        
        /* 3. Añadir Nuevo */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(3) a:before { content: "\f502"; } 
        
        /* 4. Categorías */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(4) a:before { content: "\f318"; } 
        
        /* 5. Lenguajes */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(5) a:before { content: "\f475"; } 
        
        /* 6. Acerca de */
        #menu-posts-mdq_project ul.wp-submenu li:nth-child(6) a:before { content: "\f348"; } 
    </style>
    <?php
    $admin_styles = ob_get_clean();
    echo $admin_styles;

    wp_enqueue_script( 'mdq-admin-js', MDQ_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
    
    // Inline setup logic for Media Upload
    wp_add_inline_script( 'mdq-admin-js', 'window.mdq_admin_vars = { title: "Seleccionar Imágenes para la Galería", button: "Añadir a la Galería" };' );
}
add_action( 'admin_enqueue_scripts', 'mdq_enqueue_admin_assets' );

/**
 * Activation Hook: Flush rewrite rules
 */
register_activation_hook( __FILE__, 'mdq_porfolio_activate' );
function mdq_porfolio_activate() {
    // We register the CPT first then flush
    $cpt = new MDQ_CPT();
    $cpt->register_post_types();
    $cpt->register_taxonomies();
    flush_rewrite_rules();
}

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'mdq_porfolio_deactivate' );
function mdq_porfolio_deactivate() {
    flush_rewrite_rules();
}
