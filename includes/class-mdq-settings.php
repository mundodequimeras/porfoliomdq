<?php
/**
 * Class MDQ_Settings
 *
 * Global settings panel for Porfolio MDQ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MDQ_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

    public function enqueue_admin_assets($hook) {
        if ('mdq-portfolio_page_mdq-settings' !== $hook && 'porfolio-mdq_page_mdq-settings' !== $hook) {
            // Check both possible formats for the submenu hook
            if (strpos($hook, 'mdq-settings') === false) return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'mdq-settings-js', false, array( 'wp-color-picker' ), false, true );
        wp_add_inline_script( 'mdq-settings-js', 'jQuery(document).ready(function($){ $(".mdq-color-picker").wpColorPicker(); });' );
    }

	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=mdq_project',
			__( 'Configuración', 'porfoliomdq' ),
			__( 'Configuración', 'porfoliomdq' ),
			'manage_options',
			'mdq-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'mdq_settings_group', 'mdq_archive_title' );
		register_setting( 'mdq_settings_group', 'mdq_archive_subtitle' );
		register_setting( 'mdq_settings_group', 'mdq_portfolio_btn_text' );
		register_setting( 'mdq_settings_group', 'mdq_bg_color' );
		register_setting( 'mdq_settings_group', 'mdq_margin_top' );
		register_setting( 'mdq_settings_group', 'mdq_margin_bottom' );
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><i class="dashicons dashicons-admin-generic" style="font-size: 32px; width: 32px; height: 32px; margin-right: 10px;"></i> <?php _e( 'Configuración de Porfolio MDQ', 'porfoliomdq' ); ?></h1>
            <hr>
            
			<form method="post" action="options.php" style="max-width: 800px; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px;">
				<?php settings_fields( 'mdq_settings_group' ); ?>
				<?php do_settings_sections( 'mdq_settings_group' ); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Título del Archivo', 'porfoliomdq' ); ?></th>
						<td>
                            <input type="text" name="mdq_archive_title" value="<?php echo esc_attr( get_option( 'mdq_archive_title', __( 'Proyectos', 'porfoliomdq' ) ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'El título principal que aparece en la página de listado de proyectos.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><?php _e( 'Subtítulo del Archivo', 'porfoliomdq' ); ?></th>
						<td>
                            <textarea name="mdq_archive_subtitle" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'mdq_archive_subtitle', __( 'Explora mis proyectos y creaciones profesionales.', 'porfoliomdq' ) ) ); ?></textarea>
                            <p class="description"><?php _e( 'Breve descripción que aparece bajo el título principal.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>

                    <tr valign="top">
						<th scope="row"><?php _e( 'Texto del Botón "Ver Más"', 'porfoliomdq' ); ?></th>
						<td>
                            <input type="text" name="mdq_portfolio_btn_text" value="<?php echo esc_attr( get_option( 'mdq_portfolio_btn_text', __( 'Ver Caso de Estudio', 'porfoliomdq' ) ) ); ?>" class="regular-text" />
                            <p class="description"><?php _e( 'El texto que aparece en los botones de las tarjetas del portafolio.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>

                    <tr><td colspan="2"><hr><h3><?php _e( 'Apariencia y Espaciado', 'porfoliomdq' ); ?></h3></td></tr>

                    <tr valign="top">
						<th scope="row"><?php _e( 'Color de Fondo', 'porfoliomdq' ); ?></th>
						<td>
                            <input type="text" name="mdq_bg_color" value="<?php echo esc_attr( get_option( 'mdq_bg_color', '#f8fafc' ) ); ?>" class="mdq-color-picker" />
                            <p class="description"><?php _e( 'Define el color de fondo de la sección principal del portafolio.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>

                    <tr valign="top">
						<th scope="row"><?php _e( 'Espacio Superior (px)', 'porfoliomdq' ); ?></th>
						<td>
                            <input type="number" name="mdq_margin_top" value="<?php echo esc_attr( get_option( 'mdq_margin_top', '40' ) ); ?>" class="small-text" />
                            <p class="description"><?php _e( 'Margen superior para evitar que se pegue al encabezado.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>

                    <tr valign="top">
						<th scope="row"><?php _e( 'Espacio Inferior (px)', 'porfoliomdq' ); ?></th>
						<td>
                            <input type="number" name="mdq_margin_bottom" value="<?php echo esc_attr( get_option( 'mdq_margin_bottom', '60' ) ); ?>" class="small-text" />
                            <p class="description"><?php _e( 'Margen inferior para separar del contenido siguiente.', 'porfoliomdq' ); ?></p>
                        </td>
					</tr>
				</table>
				
				<?php submit_button( __( 'Guardar Cambios Profesionales', 'porfoliomdq' ) ); ?>
			</form>
		</div>
		<?php
	}
}

new MDQ_Settings();
