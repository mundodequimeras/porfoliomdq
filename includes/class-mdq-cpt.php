<?php
/**
 * Class MDQ_CPT
 *
 * Registration of Custom Post Types and Taxonomies for Porfolio MDQ
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MDQ_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 10 );
		add_action( 'init', array( $this, 'register_taxonomies' ), 10 );
        add_filter( 'template_include', array( $this, 'portfolio_templates' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_dashboard_settings' ) );
	}

    /**
     * Register Dashboard Settings (Legacy or minimal)
     */
    public function register_dashboard_settings() {
        // We moved most settings to MDQ_Settings, but we might keep some here if needed.
    }

    /**
     * Admin Menu
     */
    public function admin_menu() {
        // Add Dashboard under Projects
        add_submenu_page(
            'edit.php?post_type=mdq_project',
            __( 'Dashboard', 'porfoliomdq' ),
            __( 'Dashboard', 'porfoliomdq' ),
            'manage_options',
            'mdq_dashboard',
            array( $this, 'render_dashboard' )
        );

        // Sorting the menu items
        add_action( 'admin_menu', array( $this, 'reorder_admin_menu' ), 999 );
    }

    /**
     * Reorder Admin Submenu
     */
    public function reorder_admin_menu() {
        global $submenu;
        $parent = 'edit.php?post_type=mdq_project';
        
        if ( ! isset( $submenu[$parent] ) ) return;

        $new_order = array();
        $about_item = null;
        $dashboard_item = null;

        // Find our special items
        foreach ( $submenu[$parent] as $key => $item ) {
            if ( $item[2] === 'mdq_dashboard' ) {
                $dashboard_item = $item;
                unset($submenu[$parent][$key]);
            } elseif ( $item[2] === 'mdq-about' ) {
                $about_item = $item;
                unset($submenu[$parent][$key]);
            }
        }

        // 1. Dashboard first
        if ( $dashboard_item ) $new_order[] = $dashboard_item;

        // 2. Default items (All projects, Add New, Taxonomies)
        foreach ( $submenu[$parent] as $item ) {
            if ( $item[2] === 'edit.php?post_type=mdq_project' ) {
                $item[0] = __( 'Todos los Proyectos', 'porfoliomdq' );
            }
            $new_order[] = $item;
        }

        // 3. About last
        if ( $about_item ) $new_order[] = $about_item;

        $submenu[$parent] = array_values($new_order);
    }


    /**
     * Render Admin Dashboard
     */
    public function render_dashboard() {
        ?>
        <div class="wrap">
            <h1 style="font-weight: 800; color: #1f2937; margin-bottom: 30px;"><?php _e( 'Dashboard - Porfolio MDQ', 'porfoliomdq' ); ?></h1>
            
            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
                <div class="updated notice is-dismissible"><p><?php _e( 'Configuración guardada exitosamente.', 'porfoliomdq' ); ?></p></div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; max-width: 1200px;">
                <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                        <h3 style="margin: 0; color: #1f2937;"><?php _e( 'Centro de Control del Portafolio', 'porfoliomdq' ); ?></h3>
                        <a href="<?php echo admin_url('edit.php?post_type=mdq_project&page=mdq-settings'); ?>" class="button button-primary"><?php _e( 'Ir a Configuración Global', 'porfoliomdq' ); ?></a>
                    </div>

                    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #f3f4f6;">

                    <div style="font-size: 16px; line-height: 1.6; color: #4b5563;">
                        <h3 style="color: #1f2937;"><?php _e( 'Instrucciones de Uso', 'porfoliomdq' ); ?></h3>
                        <p><?php _e( 'Bienvenido, Diego. Tu portfolio profesional está listo para brillar.', 'porfoliomdq' ); ?></p>
                        
                        <div style="background: #f3f4f6; padding: 25px; border-radius: 10px; margin: 30px 0;">
                            <h4 style="margin-top: 0;"><?php _e( 'Shortcode Principal', 'porfoliomdq' ); ?></h4>
                            <p><?php _e( 'Usa este código en cualquier página o entrada:', 'porfoliomdq' ); ?></p>
                            <code>[porfolio_mdq_view]</code>
                        </div>

                        <div style="background: #eef2ff; padding: 25px; border-radius: 10px; margin: 30px 0; border: 1px solid #c7d2fe;">
                            <h4 style="margin-top: 0; color: #4338ca;"><?php _e( 'Generador de Shortcode Personalizado', 'porfoliomdq' ); ?></h4>
                            <p><?php _e( 'Filtra tus proyectos por categoría o lenguaje:', 'porfoliomdq' ); ?></p>
                            
                            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                                <div style="flex: 1;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e( 'Categoría', 'porfoliomdq' ); ?></label>
                                    <select id="mdq_gen_cat" style="width: 100%; padding: 8px; border-radius: 6px;">
                                        <option value=""><?php _e( 'Todas', 'porfoliomdq' ); ?></option>
                                        <?php
                                        $all_cats = get_terms( array( 'taxonomy' => 'mdq_category', 'hide_empty' => false ) );
                                        if ( ! is_wp_error( $all_cats ) ) {
                                            foreach ( $all_cats as $cat ) echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e( 'Lenguaje', 'porfoliomdq' ); ?></label>
                                    <select id="mdq_gen_lang" style="width: 100%; padding: 8px; border-radius: 6px;">
                                        <option value=""><?php _e( 'Todos', 'porfoliomdq' ); ?></option>
                                        <?php
                                        $all_langs = get_terms( array( 'taxonomy' => 'mdq_language', 'hide_empty' => false ) );
                                        if ( ! is_wp_error( $all_langs ) ) {
                                            foreach ( $all_langs as $lang ) echo '<option value="' . esc_attr( $lang->slug ) . '">' . esc_html( $lang->name ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div style="background: #1e293b; padding: 15px; border-radius: 8px; color: #fff; display: flex; align-items: center; justify-content: space-between;">
                                <code id="mdq_shortcode_res" style="background: transparent; color: #38bdf8; font-family: monospace; font-size: 14px;">[porfolio_mdq_view]</code>
                                <button type="button" onclick="mdqCopyShortcode()" class="button" style="background: #334155; color: #fff; border: 0; cursor: pointer; height: auto; line-height: 1; padding: 10px 15px;"><?php _e( 'Copiar', 'porfoliomdq' ); ?></button>
                            </div>

                            <script>
                            function updateMDQShortcode() {
                                var cat = jQuery('#mdq_gen_cat').val();
                                var lang = jQuery('#mdq_gen_lang').val();
                                var code = '[porfolio_mdq_view';
                                if (cat) code += ' category="' + cat + '"';
                                if (lang) code += ' language="' + lang + '"';
                                code += ']';
                                jQuery('#mdq_shortcode_res').text(code);
                            }
                            jQuery('#mdq_gen_cat, #mdq_gen_lang').on('change', updateMDQShortcode);
                            function mdqCopyShortcode() {
                                var text = jQuery('#mdq_shortcode_res').text();
                                navigator.clipboard.writeText(text);
                                alert('<?php _e( 'Shortcode copiado al portapapeles', 'porfoliomdq' ); ?>');
                            }
                            </script>
                        </div>

                    <div style="background: #eef2ff; padding: 25px; border-radius: 10px; margin: 30px 0; border: 1px solid #c7d2fe;">
                        <h4 style="margin-top: 0; color: #4338ca;"><?php _e( 'Página Principal Automática', 'porfoliomdq' ); ?></h4>
                        <p><?php _e( 'Tu porfolio ya tiene una URL principal:', 'porfoliomdq' ); ?></p>
                        <a href="<?php echo home_url('/proyectos'); ?>" target="_blank" style="font-weight: 600; text-decoration: none; color: #4338ca; border-bottom: 2px solid #4338ca;">
                            <?php echo home_url('/proyectos'); ?>
                        </a>
                    </div>
                </div>

                <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 30px; grid-column: span 2;">
                    <h3 style="margin-top: 0; color: #1f2937;"><?php _e( 'Estadísticas de Descargas', 'porfoliomdq' ); ?></h3>
                    <p class="description"><?php _e( 'Seguimiento de clics en los botones de descarga de tus proyectos.', 'porfoliomdq' ); ?></p>
                    
                    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <thead>
                            <tr>
                                <th style="font-weight: 700; padding: 15px;"><?php _e( 'Proyecto', 'porfoliomdq' ); ?></th>
                                <th style="font-weight: 700; padding: 15px; text-align: center;"><?php _e( 'Web', 'porfoliomdq' ); ?></th>
                                <th style="font-weight: 700; padding: 15px; text-align: center;"><?php _e( 'GitHub', 'porfoliomdq' ); ?></th>
                                <th style="font-weight: 700; padding: 15px; text-align: center;"><?php _e( 'Tienda', 'porfoliomdq' ); ?></th>
                                <th style="font-weight: 700; padding: 15px; text-align: center;"><?php _e( 'Total', 'porfoliomdq' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $projects = get_posts( array( 'post_type' => 'mdq_project', 'posts_per_page' => -1 ) );
                            if ( $projects ) :
                                foreach ( $projects as $project ) :
                                    $web    = (int) get_post_meta( $project->ID, '_mdq_download_count_web', true );
                                    $github = (int) get_post_meta( $project->ID, '_mdq_download_count_github', true );
                                    $store  = (int) get_post_meta( $project->ID, '_mdq_download_count_store', true );
                                    $total  = $web + $github + $store;
                                    ?>
                                    <tr>
                                        <td style="padding: 15px; font-weight: 500;"><?php echo get_the_title( $project->ID ); ?></td>
                                        <td style="padding: 15px; text-align: center; color: #2563eb; font-weight: 600;"><?php echo $web; ?></td>
                                        <td style="padding: 15px; text-align: center; color: #1e293b; font-weight: 600;"><?php echo $github; ?></td>
                                        <td style="padding: 15px; text-align: center; color: #7e22ce; font-weight: 600;"><?php echo $store; ?></td>
                                        <td style="padding: 15px; text-align: center; font-weight: 700; background: #f8fafc;"><?php echo $total; ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                            else :
                                ?>
                                <tr><td colspan="5" style="padding: 20px; text-align: center;"><?php _e( 'No hay proyectos registrados.', 'porfoliomdq' ); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Handle Templates
     */
    public function portfolio_templates( $template ) {
        if ( is_post_type_archive( 'mdq_project' ) || is_tax( array( 'mdq_category', 'mdq_language' ) ) ) {
            $custom_template = plugin_dir_path( __FILE__ ) . '../templates/archive-portfolio.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

	/**
	 * Register Custom Post Types
	 */
	public function register_post_types() {
		$labels = array(
			'name'               => _x( 'Proyectos', 'post type general name', 'porfoliomdq' ),
			'singular_name'      => _x( 'Proyecto', 'post type singular name', 'porfoliomdq' ),
			'menu_name'          => _x( 'Porfolio MDQ', 'admin menu', 'porfoliomdq' ),
			'add_new'            => _x( 'Añadir Nuevo', 'proyecto', 'porfoliomdq' ),
			'add_new_item'       => __( 'Añadir Nuevo Proyecto', 'porfoliomdq' ),
			'edit_item'          => __( 'Editar Proyecto', 'porfoliomdq' ),
			'new_item'           => __( 'Nuevo Proyecto', 'porfoliomdq' ),
			'all_items'          => __( 'Todos los Proyectos', 'porfoliomdq' ),
			'view_item'          => __( 'Ver Proyecto', 'porfoliomdq' ),
			'search_items'       => __( 'Buscar Proyectos', 'porfoliomdq' ),
			'not_found'          => __( 'No se encontraron proyectos.', 'porfoliomdq' ),
			'not_found_in_trash' => __( 'No se encontraron proyectos en la papelera.', 'porfoliomdq' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true, // Standard CPT menu
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'proyectos' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true,
		);

		register_post_type( 'mdq_project', $args );
	}

	/**
	 * Register Taxonomies
	 */
	public function register_taxonomies() {
		// Categories
		$labels_cat = array(
			'name'              => _x( 'Categorías', 'taxonomy general name', 'porfoliomdq' ),
			'singular_name'     => _x( 'Categoría', 'taxonomy singular name', 'porfoliomdq' ),
			'search_items'      => __( 'Buscar Categorías', 'porfoliomdq' ),
			'all_items'         => __( 'Todas las Categorías', 'porfoliomdq' ),
			'parent_item'       => __( 'Categoría Padre', 'porfoliomdq' ),
			'parent_item_colon' => __( 'Categoría Padre:', 'porfoliomdq' ),
			'edit_item'         => __( 'Editar Categoría', 'porfoliomdq' ),
			'update_item'       => __( 'Actualizar Categoría', 'porfoliomdq' ),
			'add_new_item'      => __( 'Añadir Nueva Categoría', 'porfoliomdq' ),
			'new_item_name'     => __( 'Nombre de Nueva Categoría', 'porfoliomdq' ),
			'menu_name'         => __( 'Categorías', 'porfoliomdq' ),
		);

		$args_cat = array(
			'hierarchical'      => true,
			'labels'            => $labels_cat,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
            'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'categoria-proyecto' ),
		);

		register_taxonomy( 'mdq_category', array( 'mdq_project' ), $args_cat );

		// Languages
		$labels_lang = array(
			'name'              => _x( 'Lenguajes', 'taxonomy general name', 'porfoliomdq' ),
			'singular_name'     => _x( 'Lenguaje', 'taxonomy singular name', 'porfoliomdq' ),
			'search_items'      => __( 'Buscar Lenguajes', 'porfoliomdq' ),
			'all_items'         => __( 'Todos los Lenguajes', 'porfoliomdq' ),
			'parent_item'       => __( 'Lenguaje Padre', 'porfoliomdq' ),
			'parent_item_colon' => __( 'Lenguaje Padre:', 'porfoliomdq' ),
			'edit_item'         => __( 'Editar Lenguaje', 'porfoliomdq' ),
			'update_item'       => __( 'Actualizar Lenguaje', 'porfoliomdq' ),
			'add_new_item'      => __( 'Añadir Nuevo Lenguaje', 'porfoliomdq' ),
			'new_item_name'     => __( 'Nombre de Nuevo Lenguaje', 'porfoliomdq' ),
			'menu_name'         => __( 'Lenguajes', 'porfoliomdq' ),
		);

		$args_lang = array(
			'hierarchical'      => false,
			'labels'            => $labels_lang,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
            'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'lenguaje-programacion' ),
		);

		register_taxonomy( 'mdq_language', array( 'mdq_project' ), $args_lang );
	}
}

new MDQ_CPT();
