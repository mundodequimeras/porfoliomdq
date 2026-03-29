<?php
/**
 * Elementor Widget for Porfolio MDQ
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MDQ_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mdq_portfolio_grid';
	}

	public function get_title() {
		return __( 'Grilla de Proyectos MDQ', 'porfoliomdq' );
	}

	public function get_icon() {
		return 'eicon-post-grid';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Configuración de Grilla', 'porfoliomdq' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Título de la Sección', 'porfoliomdq' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Ej: Mis Proyectos', 'porfoliomdq' ),
				'default' => __( 'Proyectos', 'porfoliomdq' ),
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtítulo de la Sección', 'porfoliomdq' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 3,
				'placeholder' => __( 'Breve descripción...', 'porfoliomdq' ),
				'default' => __( 'Explora mis proyectos y creaciones profesionales.', 'porfoliomdq' ),
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => __( 'Límite de proyectos', 'porfoliomdq' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => -1,
			]
		);

        // Fetch Categories
        $categories = get_terms( array( 'taxonomy' => 'mdq_category', 'hide_empty' => false ) );
        $cat_options = [ '' => __( 'Todas', 'porfoliomdq' ) ];
        if ( ! is_wp_error( $categories ) ) {
            foreach ( $categories as $cat ) {
                $cat_options[ $cat->slug ] = $cat->name;
            }
        }

		$this->add_control(
			'category',
			[
				'label' => __( 'Filtrar por Categoría', 'porfoliomdq' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $cat_options,
				'default' => '',
			]
		);

        // Fetch Languages
        $languages = get_terms( array( 'taxonomy' => 'mdq_language', 'hide_empty' => false ) );
        $lang_options = [ '' => __( 'Todos', 'porfoliomdq' ) ];
        if ( ! is_wp_error( $languages ) ) {
            foreach ( $languages as $lang ) {
                $lang_options[ $lang->slug ] = $lang->name;
            }
        }

		$this->add_control(
			'language',
			[
				'label' => __( 'Filtrar por Lenguaje', 'porfoliomdq' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $lang_options,
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        if ( ! class_exists( 'MDQ_Shortcode' ) ) {
            return;
        }

        $shortcode = new MDQ_Shortcode();
        $params = array(
            'limit'    => $settings['limit'],
            'category' => $settings['category'],
            'language' => $settings['language'],
            'title'    => $settings['title'],
            'subtitle' => $settings['subtitle']
        );

        echo $shortcode->render_portfolio( $params );
	}
}
