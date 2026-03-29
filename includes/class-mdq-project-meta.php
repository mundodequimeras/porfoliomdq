<?php
/**
 * Project Meta Fields for Downloads
 */
class MDQ_Project_Meta {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_download_meta_box' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_gallery_meta_box' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_shortcode_helper_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_project_meta' ), 10, 2 );
        add_filter( 'the_content', array( $this, 'append_project_sections' ) );
        
        // Tracking Actions
        add_action( 'wp_ajax_mdq_track_download', array( $this, 'ajax_track_download' ) );
        add_action( 'wp_ajax_nopriv_mdq_track_download', array( $this, 'ajax_track_download' ) );
    }

    public function add_download_meta_box() {
        add_meta_box(
            'mdq_download_options',
            __( 'Opciones de Descarga', 'porfoliomdq' ),
            array( $this, 'render_download_meta_box' ),
            'mdq_project',
            'normal',
            'high'
        );
    }

    public function render_download_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'mdq_save_download_meta', 'mdq_project_nonce' );

        // Fetch existing metadata
        $web_link    = get_post_meta( $post->ID, '_mdq_download_web', true );
        $github_link = get_post_meta( $post->ID, '_mdq_download_github', true );
        $store_link  = get_post_meta( $post->ID, '_mdq_download_store', true );
        $store_type  = get_post_meta( $post->ID, '_mdq_download_store_type', true );

        ?>
        <div class="mdq-meta-box-wrapper">
            <style>
                .mdq-meta-field { margin-bottom: 20px; }
                .mdq-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; }
                .mdq-meta-field input[type="text"], .mdq-meta-field select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; }
                .mdq-meta-desc { font-size: 12px; color: #666; font-style: italic; margin-top: 4px; }
            </style>

            <div class="mdq-meta-field">
                <label for="mdq_project_layout"><?php _e( 'Diseño de Visualización', 'porfoliomdq' ); ?></label>
                <?php $current_layout = get_post_meta( $post->ID, '_mdq_project_layout', true ); ?>
                <select id="mdq_project_layout" name="mdq_project_layout">
                    <option value="classic" <?php selected( $current_layout, 'classic' ); ?>><?php _e( 'Clásico (Una columna)', 'porfoliomdq' ); ?></option>
                    <option value="professional" <?php selected( $current_layout, 'professional' ); ?>><?php _e( 'Moderno (Barra lateral derecha)', 'porfoliomdq' ); ?></option>
                </select>
                <p class="mdq-meta-desc"><?php _e( 'Elige cómo quieres que se vea este proyecto en el sitio web.', 'porfoliomdq' ); ?></p>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-top: 20px;">
            <div class="mdq-meta-field">
                <label for="mdq_download_web"><?php _e( 'Enlace de Descarga Directa (Web)', 'porfoliomdq' ); ?></label>
                <input type="text" id="mdq_download_web" name="mdq_download_web" value="<?php echo esc_attr( $web_link ); ?>" placeholder="https://ejemplo.com/descarga.zip">
                <p class="mdq-meta-desc"><?php _e( 'URL directa al archivo o página de descarga en tu web.', 'porfoliomdq' ); ?></p>
            </div>

            <div class="mdq-meta-field">
                <label for="mdq_download_github"><?php _e( 'Repositorio GitHub', 'porfoliomdq' ); ?></label>
                <input type="text" id="mdq_download_github" name="mdq_download_github" value="<?php echo esc_attr( $github_link ); ?>" placeholder="https://github.com/usuario/repositorio">
                <p class="mdq-meta-desc"><?php _e( 'Enlace al repositorio oficial del proyecto.', 'porfoliomdq' ); ?></p>
            </div>

            <div class="mdq-meta-field">
                <label for="mdq_demo_url"><?php _e( 'Enlace de Visualización (Demo en Vivo)', 'porfoliomdq' ); ?></label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="mdq_demo_url" name="mdq_demo_url" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mdq_demo_url', true ) ); ?>" placeholder="https://ejemplo.com/demo" style="flex: 1;">
                    <input type="text" id="mdq_demo_text" name="mdq_demo_text" value="<?php echo esc_attr( get_post_meta( $post->ID, '_mdq_demo_text', true ) ? get_post_meta( $post->ID, '_mdq_demo_text', true ) : __( 'Ver Demo', 'porfoliomdq' ) ); ?>" placeholder="<?php _e( 'Texto del botón', 'porfoliomdq' ); ?>" style="width: 150px;">
                </div>
                <p class="mdq-meta-desc"><?php _e( 'Si tu proyecto se puede visualizar online, añade el enlace y personaliza el nombre del botón.', 'porfoliomdq' ); ?></p>
            </div>

            <div class="mdq-meta-field">
                <label for="mdq_download_store"><?php _e( 'Enlace a Tienda Oficial', 'porfoliomdq' ); ?></label>
                <div style="display: flex; gap: 10px;">
                    <select id="mdq_download_store_type" name="mdq_download_store_type" style="width: 150px;">
                        <option value="wordpress" <?php selected( $store_type, 'wordpress' ); ?>>WordPress</option>
                        <option value="joomla" <?php selected( $store_type, 'joomla' ); ?>>Joomla</option>
                        <option value="glpi" <?php selected( $store_type, 'glpi' ); ?>>GLPI</option>
                        <option value="other" <?php selected( $store_type, 'other' ); ?>>Otra Tienda</option>
                    </select>
                    <input type="text" id="mdq_download_store" name="mdq_download_store" value="<?php echo esc_attr( $store_link ); ?>" placeholder="https://wordpress.org/plugins/...">
                </div>
                <p class="mdq-meta-desc"><?php _e( 'Enlace al marketplace donde esté publicado el plugin.', 'porfoliomdq' ); ?></p>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-top: 20px;">
            <div class="mdq-meta-field" style="display: flex; align-items: center; gap: 10px;">
                <?php $show_social = get_post_meta( $post->ID, '_mdq_show_social', true ); ?>
                <input type="checkbox" id="mdq_show_social" name="mdq_show_social" value="yes" <?php checked( $show_social, 'yes' ); ?> <?php if ($show_social === '') echo 'checked'; // Default to checked ?>>
                <label for="mdq_show_social" style="margin-bottom: 0;"><?php _e( 'Mostrar botones de "Compartir en Redes Sociales"', 'porfoliomdq' ); ?></label>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-top: 20px;">
            <h3 style="margin-bottom: 15px;"><?php _e( 'Configuración de Donación (PayPal)', 'porfoliomdq' ); ?></h3>
            
            <div class="mdq-meta-field" style="display: flex; align-items: center; gap: 10px;">
                <?php $don_enabled = get_post_meta( $post->ID, '_mdq_donation_enabled', true ); ?>
                <input type="checkbox" id="mdq_donation_enabled" name="mdq_donation_enabled" value="yes" <?php checked( $don_enabled, 'yes' ); ?>>
                <label for="mdq_donation_enabled" style="margin-bottom: 0;"><strong><?php _e( 'Activar opción de donación para este proyecto', 'porfoliomdq' ); ?></strong></label>
            </div>

            <div class="mdq-meta-field">
                <label for="mdq_donation_url"><?php _e( 'URL de Donación (PayPal)', 'porfoliomdq' ); ?></label>
                <?php $don_url = get_post_meta( $post->ID, '_mdq_donation_url', true ); ?>
                <input type="text" id="mdq_donation_url" name="mdq_donation_url" value="<?php echo esc_url( $don_url ); ?>" placeholder="https://www.paypal.com/donate?hosted_button_id=...">
                <p class="mdq-meta-desc"><?php _e( 'Enlace directo a tu página de donación o botón de PayPal.', 'porfoliomdq' ); ?></p>
            </div>

            <div class="mdq-meta-field">
                <label for="mdqdonmsg"><?php _e( 'Mensaje Personalizado (Enriquecido)', 'porfoliomdq' ); ?></label>
                <?php 
                $don_text = get_post_meta( $post->ID, '_mdq_donation_text', true ); 
                wp_editor( $don_text, 'mdqdonmsg', array( 
                    'textarea_name' => 'mdq_donation_text', 
                    'media_buttons' => false, 
                    'textarea_rows' => 8,
                    'tinymce'       => true,
                    'quicktags'     => true
                ) );
                ?>
                <p class="mdq-meta-desc"><?php _e( 'Este texto aparecerá resaltado con formato enriquecido.', 'porfoliomdq' ); ?></p>
            </div>
        </div>
        <?php
    }

    public function add_gallery_meta_box() {
        add_meta_box(
            'mdq_project_gallery',
            __( 'Galería del Proyecto', 'porfoliomdq' ),
            array( $this, 'render_gallery_meta_box' ),
            'mdq_project',
            'normal',
            'high'
        );
    }

    public function render_gallery_meta_box( $post ) {
        $gallery_ids = get_post_meta( $post->ID, '_mdq_project_gallery', true );
        $image_ids = array_filter( explode( ',', (string)$gallery_ids ) );
        ?>
        <div class="mdq-gallery-wrapper">
            <input type="hidden" id="mdq_gallery_ids" name="mdq_project_gallery" value="<?php echo esc_attr( $gallery_ids ); ?>">
            <div id="mdq-gallery-container" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                <?php
                if ( ! empty( $image_ids ) ) {
                    foreach ( $image_ids as $img_id ) {
                        $img_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                        if ( $img_url ) {
                            echo '<div class="mdq-gallery-item" data-id="' . $img_id . '" style="position: relative; width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">';
                            echo '<img src="' . esc_url( $img_url ) . '" style="width:100%; height:100%; object-fit:cover;">';
                            echo '<a href="#" class="mdq-remove-img" style="position: absolute; top:0; right:0; background: #ef4444; color: white; width: 20px; height: 20px; line-height: 20px; text-align: center; text-decoration: none; font-size: 10px;">&times;</a>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <button type="button" id="mdq_add_gallery" class="button button-primary" style="margin-bottom: 15px;"><?php _e( 'Gestionar Imágenes', 'porfoliomdq' ); ?></button>
            
            <div class="mdq-video-field" style="border-top: 1px solid #eee; padding-top: 15px;">
                <label for="mdq_project_video_url" style="display: block; font-weight: 600; margin-bottom: 5px;"><?php _e( 'URL de Video (YouTube/Vimeo)', 'porfoliomdq' ); ?></label>
                <?php $video_url = get_post_meta( $post->ID, '_mdq_project_video_url', true ); ?>
                <input type="text" id="mdq_project_video_url" name="mdq_project_video_url" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://www.youtube.com/watch?v=..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                <p class="description"><?php _e( 'Si añades un video, aparecerá como el primer elemento de tu galería.', 'porfoliomdq' ); ?></p>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var mdq_gallery_frame;
            $('#mdq_add_gallery').on('click', function(e) {
                e.preventDefault();
                if (mdq_gallery_frame) { mdq_gallery_frame.open(); return; }
                mdq_gallery_frame = wp.media({
                    title: '<?php _e( "Seleccionar Imágenes para la Galería", "porfoliomdq" ); ?>',
                    button: { text: '<?php _e( "Usar en Galería", "porfoliomdq" ); ?>' },
                    library: { type: 'image' },
                    multiple: 'add'
                });
                mdq_gallery_frame.on('select', function() {
                    var selection = mdq_gallery_frame.state().get('selection');
                    var ids = [];
                    $('#mdq-gallery-container').empty();
                    selection.each(function(attachment) {
                        attachment = attachment.toJSON();
                        ids.push(attachment.id);
                        var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        $('#mdq-gallery-container').append('<div class="mdq-gallery-item" data-id="'+attachment.id+'" style="position: relative; width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;"><img src="'+thumb+'" style="width:100%; height:100%; object-fit:cover;"><a href="#" class="mdq-remove-img" style="position: absolute; top:0; right:0; background: #ef4444; color: white; width: 20px; height: 20px; line-height: 20px; text-align: center; text-decoration: none; font-size: 10px;">&times;</a></div>');
                    });
                    $('#mdq_gallery_ids').val(ids.join(','));
                });
                mdq_gallery_frame.open();
            });
            $(document).on('click', '.mdq-remove-img', function(e) {
                e.preventDefault();
                var item = $(this).closest('.mdq-gallery-item');
                item.remove();
                var ids = [];
                $('.mdq-gallery-item').each(function() { ids.push($(this).data('id')); });
                $('#mdq_gallery_ids').val(ids.join(','));
            });
        });
        </script>
        <?php
    }

    public function save_project_meta( $post_id, $post ) {
        // Only run if our CPT
        if ( $post->post_type !== 'mdq_project' ) {
            return;
        }

        // Security checks
        if ( ! isset( $_POST['mdq_project_nonce'] ) || ! wp_verify_nonce( $_POST['mdq_project_nonce'], 'mdq_save_download_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Gallery
        if ( isset( $_POST['mdq_project_gallery'] ) ) {
            $gallery_data = sanitize_text_field( $_POST['mdq_project_gallery'] );
            update_post_meta( $post_id, '_mdq_project_gallery', $gallery_data );
        }

        // Field definitions for the loop
        $fields = array(
            'mdq_download_web',
            'mdq_download_github',
            'mdq_download_store',
            'mdq_download_store_type',
            'mdq_project_layout',
            'mdq_show_social',
            'mdq_donation_enabled',
            'mdq_donation_url',
            'mdq_demo_url',
            'mdq_demo_text',
            'mdq_project_video_url'
        );

        foreach ( $fields as $field ) {
            // Checkbox logic
            if ( in_array( $field, array( 'mdq_show_social', 'mdq_donation_enabled' ) ) ) {
                $val = isset( $_POST[$field] ) ? 'yes' : 'no';
                update_post_meta( $post_id, '_' . $field, $val );
                continue;
            }

            // Normal and URL fields
            if ( isset( $_POST[$field] ) ) {
                $val = $_POST[$field];
                if ( strpos( $field, 'url' ) !== false ) {
                    update_post_meta( $post_id, '_' . $field, esc_url_raw( $val ) );
                } else {
                    update_post_meta( $post_id, '_' . $field, sanitize_text_field( $val ) );
                }
            }
        }

        // Special handling for the Enriched Donation Text (kses)
        if ( isset( $_POST['mdq_donation_text'] ) ) {
            update_post_meta( $post_id, '_mdq_donation_text', wp_kses_post( $_POST['mdq_donation_text'] ) );
        }
    }

    public function save_download_meta( $post_id ) {
        // This is now handled by save_project_meta to unify logic
    }

    public function append_project_sections( $content ) {
        if ( ! is_singular( 'mdq_project' ) ) {
            return $content;
        }

        $post_id = get_the_ID();
        $layout  = get_post_meta( $post_id, '_mdq_project_layout', true );
        
        // 0. Video Section (Added to gallery)
        $video_url = get_post_meta( $post_id, '_mdq_project_video_url', true );
        
        // 1. Gallery Section
        $gallery_ids = get_post_meta( $post_id, '_mdq_project_gallery', true );
        $image_ids = array_filter( explode( ',', (string)$gallery_ids ) );
        $gallery_html = '';

        if ( ! empty( $image_ids ) || ! empty( $video_url ) ) {
            $gallery_html .= '<div class="mdq-project-gallery-section">';
            $gallery_html .= '<h2>' . __( 'Galería Multimedia', 'porfoliomdq' ) . '</h2>';
            $gallery_html .= '<div class="mdq-gallery-grid-display">';
            
            // Render Video First
            if ( ! empty( $video_url ) ) {
                // Try to get YouTube/Vimeo ID for thumbnail (simplified)
                $video_thumb = MDQ_PLUGIN_URL . 'assets/images/video-placeholder.png'; 
                // We'll use a CSS-based placeholder if we don't have a helper for thubnails yet
                $gallery_html .= '<a href="' . esc_url( $video_url ) . '" class="mdq-gallery-link glightbox mdq-video-link">';
                $gallery_html .= '<div class="mdq-video-placeholder"><i class="fas fa-play"></i><span>' . __('Ver Video', 'porfoliomdq') . '</span></div>';
                $gallery_html .= '</a>';
            }

            foreach ( $image_ids as $img_id ) {
                $img_full = wp_get_attachment_image_url( $img_id, 'full' );
                $img_thumb = wp_get_attachment_image( $img_id, 'large', false, array('class' => 'mdq-gallery-img') );
                if ( $img_full && $img_thumb ) {
                    $gallery_html .= '<a href="' . esc_url( $img_full ) . '" class="mdq-gallery-link glightbox">';
                    $gallery_html .= $img_thumb;
                    $gallery_html .= '</a>';
                }
            }
            $gallery_html .= '</div>';
            $gallery_html .= '</div>';
        }

        // 2. Download Section (Internal helper variable)
        $web_link    = get_post_meta( $post_id, '_mdq_download_web', true );
        $github_link = get_post_meta( $post_id, '_mdq_download_github', true );
        $store_link  = get_post_meta( $post_id, '_mdq_download_store', true );
        $store_type  = get_post_meta( $post_id, '_mdq_download_store_type', true );
        $demo_link   = get_post_meta( $post_id, '_mdq_demo_url', true );
        $demo_txt    = get_post_meta( $post_id, '_mdq_demo_text', true );
        if ( empty( $demo_txt ) ) $demo_txt = __( 'Ver Demo', 'porfoliomdq' );

        $download_box = '';
        if ( ! empty( $web_link ) || ! empty( $github_link ) || ! empty( $store_link ) || ! empty( $demo_link ) ) {
            $download_box .= '<div class="mdq-download-buttons-grid' . ($layout === 'professional' ? ' mdq-stack-vertical' : '') . '">';
            
            // Demo Button FIRST (usually more attractive)
            if ( ! empty( $demo_link ) ) {
                $download_box .= '<a href="' . esc_url( $demo_link ) . '" class="mdq-dl-btn mdq-dl-demo" target="_blank"><i class="fas fa-eye"></i> ' . esc_html( $demo_txt ) . '</a>';
            }

            if ( ! empty( $web_link ) ) {
                $download_box .= '<a href="' . esc_url( $web_link ) . '" class="mdq-dl-btn mdq-dl-web" target="_blank"><i class="fas fa-download"></i> ' . __( 'Descarga', 'porfoliomdq' ) . '</a>';
            }
            if ( ! empty( $github_link ) ) {
                $download_box .= '<a href="' . esc_url( $github_link ) . '" class="mdq-dl-btn mdq-dl-github" target="_blank"><i class="fab fa-github"></i> ' . __( 'GitHub', 'porfoliomdq' ) . '</a>';
            }
            if ( ! empty( $store_link ) ) {
                $icon = 'fas fa-link';
                if ($store_type === 'glpi') $icon = 'fas fa-plug';
                if ($store_type === 'wordpress') $icon = 'fab fa-wordpress';
                $download_box .= '<a href="' . esc_url( $store_link ) . '" class="mdq-dl-btn mdq-dl-store" target="_blank"><i class="' . esc_attr($icon) . '"></i> ' . __( 'Marketplace', 'porfoliomdq' ) . '</a>';
            }
            $download_box .= '</div>';
        }

        // Add Related Projects at the very bottom
        $related_html = $this->get_related_projects_html( $post_id );

        // Modern Layout Processing
        if ( $layout === 'professional' ) {
            $bg_color    = get_option( 'mdq_bg_color', '#f8fafc' );
            $margin_top  = get_option( 'mdq_margin_top', '40' );
            $margin_bot  = get_option( 'mdq_margin_bottom', '60' );

            $dynamic_style = sprintf(
                'background-color: %s; margin-top: %spx; margin-bottom: %spx;',
                esc_attr( $bg_color ),
                intval( $margin_top ),
                intval( $margin_bot )
            );

            $out = '<div class="mdq-project-professional-wrapper" style="' . $dynamic_style . '">';
            $out .= '<div class="mdq-project-main-grid">';
            
            // Left Content
            $out .= '<div class="mdq-project-main-content">';
            
            // Featured Image as Cover
            if ( has_post_thumbnail( $post_id ) ) {
                $out .= '<div class="mdq-project-cover">';
                $out .= get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'mdq-project-featured' ) );
                $out .= '</div>';
            }

            $out .= '<div class="mdq-project-description-card">';
            $out .= '<h1 class="mdq-internal-title">' . get_the_title( $post_id ) . '</h1>';
            $out .= $content;
            $out .= '</div>';
            $out .= $gallery_html;
            $out .= '</div>';

            // Right Sidebar Shadows
            $out .= '<aside class="mdq-project-sidebar">';
            
            // Meta Box: Details
            $out .= '<div class="mdq-sidebar-widget">';
            $out .= '<h3>' . __( 'Información', 'porfoliomdq' ) . '</h3>';
            $out .= '<div class="mdq-sidebar-info">';
            
            $cats = get_the_term_list( $post_id, 'mdq_category', '', ', ' );
            if ($cats) $out .= '<div class="mdq-info-row mdq-taxonomy-row"><strong>' . __('Categoría:', 'porfoliomdq') . '</strong> <div class="mdq-tax-list">' . $cats . '</div></div>';
            
            $lang = get_the_term_list( $post_id, 'mdq_language', '', ', ' );
            if ($lang) $out .= '<div class="mdq-info-row mdq-taxonomy-row"><strong>' . __('Lenguaje:', 'porfoliomdq') . '</strong> <div class="mdq-tax-list">' . $lang . '</div></div>';
            
            $out .= '</div>'; // close info
            $out .= '</div>'; // close widget

            // Meta Box: Downloads
            if ( ! empty( $download_box ) ) {
                $out .= '<div class="mdq-sidebar-widget mdq-special-cta">';
                $out .= '<h3>' . __( 'Enlaces Oficiales', 'porfoliomdq' ) . '</h3>';
                $out .= $download_box;
                $out .= '</div>';
            }

            // Meta Box: Social Share
            $show_social = get_post_meta( $post_id, '_mdq_show_social', true );
            if ( $show_social !== 'no' ) {
                $out .= '<div class="mdq-sidebar-widget">';
                $out .= '<h3>' . __( 'Compartir', 'porfoliomdq' ) . '</h3>';
                $out .= $this->render_social_share( $post_id );
                $out .= '</div>';
            }

            // Meta Box: Donaciones (Unique Section)
            $don_enabled = get_post_meta( $post_id, '_mdq_donation_enabled', true );
            if ( $don_enabled === 'yes' ) {
                $don_url  = get_post_meta( $post_id, '_mdq_donation_url', true );
                $don_text = get_post_meta( $post_id, '_mdq_donation_text', true );
                if ( empty( $don_text ) ) $don_text = __( 'Apoya este proyecto', 'porfoliomdq' );

                $out .= '<div class="mdq-sidebar-widget mdq-donation-widget">';
                $out .= '<h3>' . __( 'Donación', 'porfoliomdq' ) . '</h3>';
                $out .= '<div class="mdq-donation-msg">' . wpautop( $don_text ) . '</div>';
                $out .= '<div class="mdq-donation-footer">';
                $out .= '<a href="' . esc_url( $don_url ) . '" class="mdq-don-btn mdq-don-paypal" target="_blank"><i class="fab fa-paypal"></i> ' . __( 'Donar con PayPal', 'porfoliomdq' ) . '</a>';
                $out .= '</div>';
                $out .= '</div>';
            }

            $out .= '</aside>';
            $out .= '</div>'; // close main grid
            
            // Project Navigation (Next / Prev)
            $out .= $this->get_project_navigation_html( $post_id );

            // Append related projects after the navigation
            $out .= $related_html;
            
            $out .= $this->get_tracking_script($post_id);
            $out .= '</div>'; // close professional wrapper
            return $out;
        }

        // Default Classic Layout
        $full_download_section = '';
        if ( ! empty( $download_box ) ) {
            $full_download_section = '<div class="mdq-single-download-section"><h2>' . __( 'Descargas y Enlaces Oficiales', 'porfoliomdq' ) . '</h2>' . $download_box . '</div>';
        }
        
        // Social Share section
        $show_social = get_post_meta( $post_id, '_mdq_show_social', true );
        $social_section = '';
        if ( $show_social !== 'no' ) {
            $social_section = '<div class="mdq-single-social-section"><h3>' . __( '¿Te gustó este proyecto? ¡Compártelo!', 'porfoliomdq' ) . '</h3>' . $this->render_social_share( $post_id ) . '</div>';
        }

        // Layout Clásico (por defecto)
        $full_donation_section = '';
        $don_enabled = get_post_meta( $post_id, '_mdq_donation_enabled', true );
        if ( $don_enabled === 'yes' ) {
            $don_url  = get_post_meta( $post_id, '_mdq_donation_url', true );
            $don_text = get_post_meta( $post_id, '_mdq_donation_text', true );
            if ( empty( $don_text ) ) $don_text = __( 'Si este proyecto te ha sido útil, considera realizar una pequeña donación para apoyar su desarrollo continuo.', 'porfoliomdq' );

            $full_donation_section .= '<div class="mdq-single-donation-section">';
            $full_donation_section .= '<h3>' . __( 'Apoya el Proyecto', 'porfoliomdq' ) . '</h3>';
            $full_donation_section .= '<div class="mdq-donation-card">';
            $full_donation_section .= '<div class="mdq-donation-content">' . wpautop( $don_text ) . '</div>';
            $full_donation_section .= '<div class="mdq-donation-footer">';
            $full_donation_section .= '<a href="' . esc_url( $don_url ) . '" class="mdq-don-btn mdq-don-paypal" target="_blank"><i class="fab fa-paypal"></i> ' . __( 'Donar con PayPal', 'porfoliomdq' ) . '</a>';
            $full_donation_section .= '</div>';
            $full_donation_section .= '</div>';
            $full_donation_section .= '</div>';
        }

        $bg_color    = get_option( 'mdq_bg_color', '#f8fafc' );
        $margin_top  = get_option( 'mdq_margin_top', '40' );
        $margin_bot  = get_option( 'mdq_margin_bottom', '60' );

        $dynamic_style = sprintf(
            'background-color: %s; margin-top: %spx; margin-bottom: %spx; padding: 40px 0;',
            esc_attr( $bg_color ),
            intval( $margin_top ),
            intval( $margin_bot )
        );

        $main_output = $content . $gallery_html . $full_download_section . $full_donation_section . $social_section . $this->get_project_navigation_html( $post_id ) . $related_html . $this->get_tracking_script($post_id);
        
        return '<div class="mdq-classic-wrapper" style="' . $dynamic_style . '">' . $main_output . '</div>';
    }

    private function get_project_navigation_html( $post_id ) {
        $prev_post = get_adjacent_post( false, '', true );
        $next_post = get_adjacent_post( false, '', false );

        if ( ! $prev_post && ! $next_post ) return '';

        ob_start();
        ?>
        <div class="mdq-project-navigation">
            <div class="mdq-nav-prev">
                <?php if ( $prev_post ) : ?>
                    <a href="<?php echo get_permalink( $prev_post->ID ); ?>">
                        <span class="mdq-nav-label"><i class="fas fa-arrow-left"></i> <?php _e( 'Anterior', 'porfoliomdq' ); ?></span>
                        <span class="mdq-nav-title"><?php echo get_the_title( $prev_post->ID ); ?></span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="mdq-nav-next">
                <?php if ( $next_post ) : ?>
                    <a href="<?php echo get_permalink( $next_post->ID ); ?>">
                        <span class="mdq-nav-label"><?php _e( 'Siguiente', 'porfoliomdq' ); ?> <i class="fas fa-arrow-right"></i></span>
                        <span class="mdq-nav-title"><?php echo get_the_title( $next_post->ID ); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_social_share( $post_id ) {
        $url   = urlencode( get_permalink( $post_id ) );
        $title = urlencode( get_the_title( $post_id ) );
        
        $out = '<div class="mdq-social-share-buttons">';
        $out .= '<a href="https://api.whatsapp.com/send?text=' . $title . '%20' . $url . '" class="mdq-share-btn mdq-share-wa" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>';
        $out .= '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' . $url . '" class="mdq-share-btn mdq-share-li" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>';
        $out .= '<a href="https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url . '" class="mdq-share-btn mdq-share-tw" target="_blank" title="X (Twitter)"><i class="fab fa-x-twitter"></i></a>';
        $out .= '</div>';
        
        return $out;
    }

    private function get_related_projects_html( $post_id ) {
        // Get current post terms
        $cats = wp_get_post_terms( $post_id, 'mdq_category', array( 'fields' => 'ids' ) );
        $langs = wp_get_post_terms( $post_id, 'mdq_language', array( 'fields' => 'ids' ) );

        $args = array(
            'post_type'      => 'mdq_project',
            'posts_per_page' => 3,
            'post__not_in'   => array( $post_id ),
            'orderby'        => 'rand',
            'tax_query'      => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'mdq_category',
                    'field'    => 'id',
                    'terms'    => $cats,
                ),
                array(
                    'taxonomy' => 'mdq_language',
                    'field'    => 'id',
                    'terms'    => $langs,
                ),
            ),
        );

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        ?>
        <div class="mdq-related-projects-section">
            <h2 class="mdq-related-title"><?php _e( 'Proyectos Similares', 'porfoliomdq' ); ?></h2>
            <div class="mdq-portfolio-grid mdq-related-grid">
                <?php while ( $query->have_posts() ) : $query->the_post(); 
                    $rel_id = get_the_ID();
                    $rel_cats = wp_get_post_terms( $rel_id, 'mdq_category' );
                    $rel_langs = wp_get_post_terms( $rel_id, 'mdq_language' );
                ?>
                    <div class="mdq-portfolio-item">
                        <div class="mdq-portfolio-card">
                            <div class="mdq-portfolio-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'large' ); ?>
                                    <?php else : ?>
                                        <img src="https://via.placeholder.com/600x400?text=MDQ+Portafolio" alt="MDQ Placeholder">
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="mdq-portfolio-content">
                                <div class="mdq-portfolio-header">
                                    <h3 class="mdq-card-title">
                                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <?php if ( ! empty( $rel_cats ) ) : 
                                        $cat_color = get_term_meta( $rel_cats[0]->term_id, 'mdq_category_color', true );
                                        if ( ! $cat_color ) $cat_color = '#6366f1';
                                        $bg_color = $cat_color . '1A';
                                    ?>
                                        <span class="mdq-category-badge" style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $cat_color ); ?>;">
                                            <?php echo esc_html( $rel_cats[0]->name ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="mdq-portfolio-tags">
                                    <?php foreach ( $rel_langs as $lang ) : ?>
                                        <span class="mdq-tag-pill"><?php echo esc_html( $lang->name ); ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mdq-portfolio-footer">
                                    <a href="<?php the_permalink(); ?>" class="mdq-case-button">
                                        <?php _e( 'Ver Proyecto', 'porfoliomdq' ); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_tracking_script($post_id) {
        ob_start();
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.mdq-dl-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    let type = 'other';
                    if (this.classList.contains('mdq-dl-web')) type = 'web';
                    if (this.classList.contains('mdq-dl-github')) type = 'github';
                    if (this.classList.contains('mdq-dl-store')) type = 'store';

                    jQuery.ajax({
                        url: '<?php echo admin_url( "admin-ajax.php" ); ?>',
                        type: 'POST',
                        data: {
                            action: 'mdq_track_download',
                            post_id: <?php echo (int) $post_id; ?>,
                            type: type,
                            nonce: '<?php echo wp_create_nonce( "mdq_track_nonce" ); ?>'
                        }
                    });
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX Tracking Logic
     */
    public function ajax_track_download() {
        check_ajax_referer( 'mdq_track_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $type    = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';

        if ( ! $post_id || ! $type ) {
            wp_send_json_error();
        }

        $meta_key = '_mdq_download_count_' . $type;
        $current  = (int) get_post_meta( $post_id, $meta_key, true );
        update_post_meta( $post_id, $meta_key, $current + 1 );

        wp_send_json_success();
    }

    public function add_shortcode_helper_meta_box() {
        add_meta_box(
            'mdq_shortcode_helper',
            __( 'Ayuda de Shortcode', 'porfoliomdq' ),
            array( $this, 'render_shortcode_helper' ),
            'mdq_project',
            'side',
            'low'
        );
    }

    public function render_shortcode_helper() {
        ?>
        <div class="mdq-shortcode-helper">
            <p style="font-size: 12px;"><?php _e( 'Copia el código para mostrar proyectos filtrados:', 'porfoliomdq' ); ?></p>
            <select id="mdq_h_cat" style="width: 100%; margin-bottom: 10px;">
                <option value=""><?php _ex( 'Categoría...', 'shortcode helper', 'porfoliomdq' ); ?></option>
                <?php
                $cats = get_terms( array( 'taxonomy' => 'mdq_category', 'hide_empty' => false ) );
                foreach ( $cats as $cat ) echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
                ?>
            </select>
            <select id="mdq_h_lang" style="width: 100%; margin-bottom: 10px;">
                <option value=""><?php _ex( 'Lenguaje...', 'shortcode helper', 'porfoliomdq' ); ?></option>
                <?php
                $langs = get_terms( array( 'taxonomy' => 'mdq_language', 'hide_empty' => false ) );
                foreach ( $langs as $lang ) echo '<option value="' . esc_attr( $lang->slug ) . '">' . esc_html( $lang->name ) . '</option>';
                ?>
            </select>
            <div style="background: #f0f0f1; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 11px; margin-bottom: 10px; word-break: break-all;" id="mdq_h_res">
                [porfolio_mdq_view]
            </div>
            <button type="button" class="button button-small" style="width: 100%;" onclick="mdqCopyH()"><?php _e( 'Copiar Shortcode', 'porfoliomdq' ); ?></button>
            <script>
            function updH() {
                var c = jQuery('#mdq_h_cat').val(), l = jQuery('#mdq_h_lang').val();
                var s = '[porfolio_mdq_view';
                if(c) s += ' category="'+c+'"';
                if(l) s += ' language="'+l+'"';
                s += ']';
                jQuery('#mdq_h_res').text(s);
            }
            jQuery('#mdq_h_cat, #mdq_h_lang').on('change', updH);
            function mdqCopyH() {
                navigator.clipboard.writeText(jQuery('#mdq_h_res').text());
                alert('Copiado');
            }
            </script>
        </div>
        <?php
    }
}

new MDQ_Project_Meta();
