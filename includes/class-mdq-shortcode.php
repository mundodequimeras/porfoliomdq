<?php
/**
 * Class MDQ_Shortcode
 *
 * Shortcode to display the professional Portfolio MDQ grid.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MDQ_Shortcode {

	public function __construct() {
		add_shortcode( 'porfolio_mdq_view', array( $this, 'render_portfolio' ) );
        
        // AJAX Actions
        add_action( 'wp_ajax_mdq_filter_portfolio', array( $this, 'ajax_filter_portfolio' ) );
        add_action( 'wp_ajax_nopriv_mdq_filter_portfolio', array( $this, 'ajax_filter_portfolio' ) );
	}

	/**
	 * Render the portfolio grid
	 */
	public function render_portfolio( $atts ) {
		$atts = shortcode_atts( array(
			'limit'    => -1,
            'category' => '',
            'language' => '',
            'title'    => get_option( 'mdq_archive_title', __( 'Proyectos', 'porfoliomdq' ) ),
            'subtitle' => get_option( 'mdq_archive_subtitle', __( 'Explora mis proyectos y creaciones profesionales.', 'porfoliomdq' ) ),
		), $atts );

        $args = array(
			'post_type'      => 'mdq_project',
			'posts_per_page' => (int) $atts['limit'],
			'status'         => 'publish',
		);

        $tax_query = array();

        if ( ! empty( $atts['category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'mdq_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['category'] ),
            );
        }

        if ( ! empty( $atts['language'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'mdq_language',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['language'] ),
            );
        }

        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return '<p>' . __( 'No se encontraron proyectos.', 'porfoliomdq' ) . '</p>';
		}

		$categories = get_terms( array(
			'taxonomy'   => 'mdq_category',
			'hide_empty' => true,
		) );

        $bg_color    = get_option( 'mdq_bg_color', '#f8fafc' );
        $margin_top  = get_option( 'mdq_margin_top', '40' );
        $margin_bot  = get_option( 'mdq_margin_bottom', '60' );

        $dynamic_style = sprintf(
            'background-color: %s; margin-top: %spx; margin-bottom: %spx;',
            esc_attr( $bg_color ),
            intval( $margin_top ),
            intval( $margin_bot )
        );

		ob_start();
		?>
		<div class="mdq-portfolio-wrapper" style="<?php echo $dynamic_style; ?>">
            <?php if ( ! empty( $atts['title'] ) || ! empty( $atts['subtitle'] ) ) : ?>
                <div class="mdq-portfolio-page-header" style="text-align: center; margin-bottom: 50px;">
                    <?php if ( ! empty( $atts['title'] ) ) : ?>
                        <h2 class="mdq-section-title" style="font-size: 3rem; font-weight: 800; color: #1e293b; margin-bottom: 15px; margin-top: 0;">
                            <?php echo esc_html( $atts['title'] ); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ( ! empty( $atts['subtitle'] ) ) : ?>
                        <p class="mdq-section-subtitle" style="font-size: 1.1rem; color: #64748b; margin-bottom: 0;">
                            <?php echo esc_html( $atts['subtitle'] ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

			<!-- Filter Controls -->
			<div class="mdq-portfolio-filters">
				<button class="mdq-filter-btn active" data-filter="all"><?php _e( 'Todos', 'porfoliomdq' ); ?></button>
				<?php foreach ( $categories as $cat ) : ?>
					<button class="mdq-filter-btn" data-filter="cat-<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></button>
				<?php endforeach; ?>
			</div>

			<!-- Portfolio Grid -->
			<div class="mdq-portfolio-grid" id="mdq-portfolio-grid">
				<?php while ( $query->have_posts() ) : $query->the_post(); 
					echo $this->render_project_card( get_the_ID() );
				?>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

            <!-- Load More Container -->
            <div class="mdq-load-more-container" style="text-align: center; margin-top: 40px;">
                <?php if ( $query->max_num_pages > 1 ) : ?>
                    <button id="mdq-load-more" class="mdq-case-button" data-limit="<?php echo esc_attr($atts['limit']); ?>" data-page="1" data-max="<?php echo esc_attr($query->max_num_pages); ?>">
                        <?php _e( 'Cargar Más Proyectos', 'porfoliomdq' ); ?>
                        <i class="fas fa-plus"></i>
                    </button>
                <?php endif; ?>
            </div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Helper: Render a single project card
	 */
	public function render_project_card( $post_id ) {
		$cats = wp_get_post_terms( $post_id, 'mdq_category' );
		$langs = wp_get_post_terms( $post_id, 'mdq_language' );
		$filter_classes = '';
		foreach ( $cats as $cat ) {
			$filter_classes .= ' cat-' . $cat->slug;
		}

		ob_start();
		?>
		<div class="mdq-portfolio-item <?php echo esc_attr( $filter_classes ); ?> mdq-fade-in">
			<div class="mdq-portfolio-card">
				<div class="mdq-portfolio-image">
					<a href="<?php echo get_permalink( $post_id ); ?>">
						<?php if ( has_post_thumbnail( $post_id ) ) : ?>
							<?php echo get_the_post_thumbnail( $post_id, 'large' ); ?>
						<?php else : ?>
							<img src="https://via.placeholder.com/600x400?text=MDQ+Portafolio" alt="MDQ Placeholder">
						<?php endif; ?>
					</a>
				</div>

				<div class="mdq-portfolio-content">
					<div class="mdq-portfolio-header">
						<h3 class="mdq-card-title">
							<a href="<?php echo get_permalink( $post_id ); ?>" style="text-decoration: none; color: inherit;">
								<?php echo get_the_title( $post_id ); ?>
							</a>
						</h3>
						<?php if ( ! empty( $cats ) ) : 
							$cat_color = get_term_meta( $cats[0]->term_id, 'mdq_category_color', true );
							if ( ! $cat_color ) $cat_color = '#6366f1';
							$bg_color = $cat_color . '1A';
						?>
							<span class="mdq-category-badge" style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $cat_color ); ?>;">
								<?php echo esc_html( $cats[0]->name ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="mdq-portfolio-excerpt">
						<?php 
                        $excerpt = get_the_excerpt( $post_id );
                        echo wp_trim_words( $excerpt, 20 ); 
                        ?>
					</div>

					<div class="mdq-portfolio-tags">
						<?php foreach ( $langs as $lang ) : 
							$icon = get_term_meta( $lang->term_id, 'mdq_term_icon', true );
						?>
							<span class="mdq-tag-pill" title="<?php echo esc_attr( $lang->name ); ?>">
								<?php if ( $icon ) : ?>
									<i class="<?php echo esc_attr( $icon ); ?>"></i>
								<?php endif; ?>
								<?php echo esc_html( $lang->name ); ?>
							</span>
						<?php endforeach; ?>
					</div>

					<div class="mdq-portfolio-footer">
						<?php 
						$btn_text = get_option( 'mdq_portfolio_btn_text', __( 'Ver Caso de Estudio', 'porfoliomdq' ) );
						?>
						<a href="<?php echo get_permalink( $post_id ); ?>" class="mdq-case-button">
							<?php echo esc_html( $btn_text ); ?>
							<i class="fas fa-arrow-right"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * AJAX logic for filtering and load more
	 */
	public function ajax_filter_portfolio() {
		$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
		$page     = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$limit    = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 6;

		$args = array(
			'post_type'      => 'mdq_project',
			'posts_per_page' => $limit,
			'paged'          => $page,
			'status'         => 'publish',
		);

		if ( ! empty( $category ) && $category !== 'all' ) {
            // Remove 'cat-' prefix if present
            $slug = str_replace('cat-', '', $category);
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'mdq_category',
					'field'    => 'slug',
					'terms'    => $slug,
				),
			);
		}

		$query = new WP_Query( $args );
		$html = '';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$html .= $this->render_project_card( get_the_ID() );
			}
			wp_reset_postdata();
		}

		wp_send_json_success( array(
			'html' => $html,
			'max_pages' => $query->max_num_pages,
            'current_page' => $page
		) );
	}
}

new MDQ_Shortcode();
