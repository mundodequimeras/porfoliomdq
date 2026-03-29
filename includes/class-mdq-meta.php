<?php
/**
 * Class MDQ_Meta
 *
 * Add custom fields (icons) to Porfolio MDQ taxonomies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class MDQ_Meta {

	public function __construct() {
		// Category icons & color
		add_action( 'mdq_category_add_form_fields', array( $this, 'add_category_fields' ), 10, 2 );
		add_action( 'mdq_category_edit_form_fields', array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'edited_mdq_category', array( $this, 'save_category_fields' ), 10, 2 );
		add_action( 'create_mdq_category', array( $this, 'save_category_fields' ), 10, 2 );

		// Language icons
		add_action( 'mdq_language_add_form_fields', array( $this, 'add_icon_field' ), 10, 2 );
		add_action( 'mdq_language_edit_form_fields', array( $this, 'edit_icon_field' ), 10, 2 );
		add_action( 'edited_mdq_language', array( $this, 'save_icon_field' ), 10, 2 );
		add_action( 'create_mdq_language', array( $this, 'save_icon_field' ), 10, 2 );
	}

	/**
	 * Add fields to Category Create screen
	 */
	public function add_category_fields( $taxonomy ) {
		$this->add_icon_field( $taxonomy );
		?>
		<div class="form-field term-group">
			<label for="mdq_category_color"><?php _e( 'Color de la Categoría', 'porfoliomdq' ); ?></label>
			<input type="text" id="mdq_category_color" name="mdq_category_color" class="mdq-color-picker" value="#6366f1">
			<p class="description"><?php _e( 'Elige el color para el fondo de la etiqueta de esta categoría.', 'porfoliomdq' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add fields to Category Edit screen
	 */
	public function edit_category_fields( $term, $taxonomy ) {
		$this->edit_icon_field( $term, $taxonomy );
		$color = get_term_meta( $term->term_id, 'mdq_category_color', true );
		if ( ! $color ) $color = '#6366f1';
		?>
		<tr class="form-field term-group-wrap">
			<th scope="row"><label for="mdq_category_color"><?php _e( 'Color de la Categoría', 'porfoliomdq' ); ?></label></th>
			<td>
				<input type="text" id="mdq_category_color" name="mdq_category_color" class="mdq-color-picker" value="<?php echo esc_attr( $color ); ?>">
				<p class="description"><?php _e( 'Elige el color para el fondo de la etiqueta de esta categoría.', 'porfoliomdq' ); ?></p>
			</td>
		</tr>
		<?php
	}

    /**
	 * Save Category fields
	 */
	public function save_category_fields( $term_id, $tt_id ) {
		$this->save_icon_field( $term_id, $tt_id );
		if ( isset( $_POST['mdq_category_color'] ) ) {
			update_term_meta( $term_id, 'mdq_category_color', sanitize_hex_color( $_POST['mdq_category_color'] ) );
		}
	}

	/**
	 * Add icon field to Create screen
	 */
	public function add_icon_field( $taxonomy ) {
		?>
		<div class="form-field term-group">
			<label for="mdq_term_icon"><?php _e( 'Icono (Clase FontAwesome)', 'porfoliomdq' ); ?></label>
			<div class="mdq-icon-preview-wrapper">
                <div class="mdq-icon-preview">
                    <i id="mdq-preview-icon" class="fas fa-question"></i>
                </div>
                <div class="mdq-icon-input-area" style="flex: 1;">
                    <input type="text" id="mdq_term_icon" name="mdq_term_icon" value="" placeholder="Ej: fas fa-code">
                    <p class="description"><?php _e( 'Ingresa la clase de FontAwesome para este término.', 'porfoliomdq' ); ?></p>
                </div>
            </div>

            <div class="mdq-quick-icons">
                <div class="mdq-icon-search-wrapper">
                    <input type="text" class="mdq-icon-search" placeholder="<?php _e( 'Buscar icono...', 'porfoliomdq' ); ?>">
                    <a href="https://fontawesome.com/icons" target="_blank" class="button button-secondary"><?php _e( 'Ver todos en FA', 'porfoliomdq' ); ?></a>
                </div>
                
                <div class="mdq-icon-grid">
                    <!-- Development & IT -->
                    <div class="mdq-quick-icon" data-icon="fas fa-code" title="Code"><i class="fas fa-code"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-laptop-code" title="Dev"><i class="fas fa-laptop-code"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-terminal" title="Terminal"><i class="fas fa-terminal"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-bug" title="Bug"><i class="fas fa-bug"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-database" title="Database"><i class="fas fa-database"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-server" title="Server"><i class="fas fa-server"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-cloud" title="Cloud"><i class="fas fa-cloud"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-microchip" title="Hardware"><i class="fas fa-microchip"></i></div>
                    
                    <!-- Design & Creative -->
                    <div class="mdq-quick-icon" data-icon="fas fa-paint-brush" title="Design"><i class="fas fa-paint-brush"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-palette" title="Palette"><i class="fas fa-palette"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-pen-nib" title="Pen"><i class="fas fa-pen-nib"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-vector-square" title="Vector"><i class="fas fa-vector-square"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-icons" title="Icons"><i class="fas fa-icons"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-camera" title="Camera"><i class="fas fa-camera"></i></div>
                    
                    <!-- Mobile & Interface -->
                    <div class="mdq-quick-icon" data-icon="fas fa-mobile-alt" title="Mobile"><i class="fas fa-mobile-alt"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-tablet-alt" title="Tablet"><i class="fas fa-tablet-alt"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-desktop" title="Desktop"><i class="fas fa-desktop"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-mouse" title="Mouse"><i class="fas fa-mouse"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-keyboard" title="Keyboard"><i class="fas fa-keyboard"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-network-wired" title="Network"><i class="fas fa-network-wired"></i></div>
                    
                    <!-- Business & Web -->
                    <div class="mdq-quick-icon" data-icon="fas fa-globe" title="World"><i class="fas fa-globe"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-shopping-cart" title="Shop"><i class="fas fa-shopping-cart"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-chart-line" title="Stats"><i class="fas fa-chart-line"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-envelope" title="Mail"><i class="fas fa-envelope"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-lock" title="Security"><i class="fas fa-lock"></i></div>
                    <div class="mdq-quick-icon" data-icon="fas fa-user-tie" title="Client"><i class="fas fa-user-tie"></i></div>
                    
                    <!-- Popular Brands -->
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-php" title="PHP"><i class="fa-brands fa-php"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-js" title="JS"><i class="fa-brands fa-js"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-react" title="React"><i class="fa-brands fa-react"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-node-js" title="Node"><i class="fa-brands fa-node-js"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-python" title="Python"><i class="fa-brands fa-python"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-wordpress" title="WordPress"><i class="fa-brands fa-wordpress"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-html5" title="HTML5"><i class="fa-brands fa-html5"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-css3-alt" title="CSS3"><i class="fa-brands fa-css3-alt"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-github" title="GitHub"><i class="fa-brands fa-github"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-swift" title="Swift"><i class="fa-brands fa-swift"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-docker" title="Docker"><i class="fa-brands fa-docker"></i></div>
                    <div class="mdq-quick-icon" data-icon="fa-brands fa-laravel" title="Laravel"><i class="fa-brands fa-laravel"></i></div>
                </div>
            </div>
		</div>
		<?php
	}

	/**
	 * Add icon field to Edit screen
	 */
	public function edit_icon_field( $term, $taxonomy ) {
		$icon = get_term_meta( $term->term_id, 'mdq_term_icon', true );
		?>
		<tr class="form-field term-group-wrap">
			<th scope="row"><label for="mdq_term_icon"><?php _e( 'Icono (Clase FontAwesome)', 'porfoliomdq' ); ?></label></th>
			<td>
				<div class="mdq-icon-preview-wrapper" style="max-width: 500px;">
                    <div class="mdq-icon-preview">
                        <i id="mdq-preview-icon" class="<?php echo $icon ? esc_attr( $icon ) : 'fas fa-question'; ?>"></i>
                    </div>
                    <div class="mdq-icon-input-area" style="flex: 1;">
                        <input type="text" id="mdq_term_icon" name="mdq_term_icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="Ej: fas fa-code">
                        <p class="description"><?php _e( 'Ingresa la clase de FontAwesome para este término.', 'porfoliomdq' ); ?></p>
                    </div>
                </div>

                <div class="mdq-quick-icons">
                    <div class="mdq-icon-search-wrapper">
                        <input type="text" class="mdq-icon-search" placeholder="<?php _e( 'Buscar icono...', 'porfoliomdq' ); ?>">
                        <a href="https://fontawesome.com/icons" target="_blank" class="button button-secondary"><?php _e( 'Ver todos en FA', 'porfoliomdq' ); ?></a>
                    </div>
                    
                    <div class="mdq-icon-grid">
                        <div class="mdq-quick-icon" data-icon="fas fa-code" title="Code"><i class="fas fa-code"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-laptop-code" title="Dev"><i class="fas fa-laptop-code"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-terminal" title="Terminal"><i class="fas fa-terminal"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-bug" title="Bug"><i class="fas fa-bug"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-database" title="Database"><i class="fas fa-database"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-server" title="Server"><i class="fas fa-server"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-cloud" title="Cloud"><i class="fas fa-cloud"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-microchip" title="Hardware"><i class="fas fa-microchip"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-paint-brush" title="Design"><i class="fas fa-paint-brush"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-palette" title="Palette"><i class="fas fa-palette"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-pen-nib" title="Pen"><i class="fas fa-pen-nib"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-vector-square" title="Vector"><i class="fas fa-vector-square"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-icons" title="Icons"><i class="fas fa-icons"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-camera" title="Camera"><i class="fas fa-camera"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-mobile-alt" title="Mobile"><i class="fas fa-mobile-alt"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-tablet-alt" title="Tablet"><i class="fas fa-tablet-alt"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-desktop" title="Desktop"><i class="fas fa-desktop"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-mouse" title="Mouse"><i class="fas fa-mouse"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-keyboard" title="Keyboard"><i class="fas fa-keyboard"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-network-wired" title="Network"><i class="fas fa-network-wired"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-globe" title="World"><i class="fas fa-globe"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-shopping-cart" title="Shop"><i class="fas fa-shopping-cart"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-chart-line" title="Stats"><i class="fas fa-chart-line"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-envelope" title="Mail"><i class="fas fa-envelope"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-lock" title="Security"><i class="fas fa-lock"></i></div>
                        <div class="mdq-quick-icon" data-icon="fas fa-user-tie" title="Client"><i class="fas fa-user-tie"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-php" title="PHP"><i class="fa-brands fa-php"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-js" title="JS"><i class="fa-brands fa-js"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-react" title="React"><i class="fa-brands fa-react"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-node-js" title="Node"><i class="fa-brands fa-node-js"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-python" title="Python"><i class="fa-brands fa-python"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-wordpress" title="WordPress"><i class="fa-brands fa-wordpress"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-html5" title="HTML5"><i class="fa-brands fa-html5"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-css3-alt" title="CSS3"><i class="fa-brands fa-css3-alt"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-github" title="GitHub"><i class="fa-brands fa-github"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-swift" title="Swift"><i class="fa-brands fa-swift"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-docker" title="Docker"><i class="fa-brands fa-docker"></i></div>
                        <div class="mdq-quick-icon" data-icon="fa-brands fa-laravel" title="Laravel"><i class="fa-brands fa-laravel"></i></div>
                    </div>
                </div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the icon field
	 */
	public function save_icon_field( $term_id, $tt_id ) {
		if ( isset( $_POST['mdq_term_icon'] ) ) {
			update_term_meta( $term_id, 'mdq_term_icon', sanitize_text_field( $_POST['mdq_term_icon'] ) );
		}
	}
}

new MDQ_Meta();
