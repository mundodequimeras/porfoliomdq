<?php
/**
 * Class MDQ_Admin_About
 *
 * Provides an informative page about the plugin in the WordPress Dashboard.
 */

if (!defined('ABSPATH')) {
	exit;
}

class MDQ_Admin_About
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'register_about_page'));
	}

	public function register_about_page()
	{
		add_submenu_page(
			'edit.php?post_type=mdq_project',
			__('Acerca de Porfolio MDQ', 'porfoliomdq'),
			__('Acerca del Plugin', 'porfoliomdq'),
			'manage_options',
			'mdq-about',
			array($this, 'render_about_page')
		);
	}

	public function render_about_page()
	{
?>
		<div class="wrap mdq-about-wrap">
			<style>
				.mdq-about-content { max-width: 900px; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 20px; }
				.mdq-header-flex { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #f0f0f1; padding-bottom: 25px; }
				.mdq-logo-circle { width: 60px; height: 60px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 24px; font-weight: bold; }
				.mdq-header-title h1 { margin: 0; font-size: 28px; line-height: 1.2; }
				.mdq-header-title p { margin: 5px 0 0; color: #666; font-size: 16px; }
				.mdq-feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-top: 40px; }
				.mdq-feature-item { padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px; transition: all 0.3s ease; }
				.mdq-feature-item:hover { border-color: #6366f1; box-shadow: 0 5px 15px rgba(99, 102, 241, 0.1); }
				.mdq-feature-item i { font-size: 24px; color: #6366f1; margin-bottom: 15px; display: block; }
				.mdq-feature-item h3 { margin: 0 0 10px; }
				.mdq-feature-item p { margin: 0; color: #666; font-size: 14px; line-height: 1.5; }
				.mdq-shortcode-box { background: #f8fafc; padding: 25px; border-radius: 10px; border-left: 5px solid #6366f1; margin-top: 40px; }
				.mdq-code-display { background: #1e293b; color: #fff; padding: 15px; border-radius: 6px; font-family: monospace; display: block; margin: 15px 0; }
				.mdq-footer-about { margin-top: 50px; text-align: center; color: #94a3b8; font-size: 13px; }
				.mdq-footer-about a { color: #6366f1; text-decoration: none; font-weight: 600; }
			</style>

			<div class="mdq-about-content">
				<div class="mdq-header-flex">
					<div class="mdq-logo-circle">M</div>
					<div class="mdq-header-title">
						<h1>Porfolio MDQ <span style="font-size: 14px; background: #6366f1; color: #fff; padding: 3px 10px; border-radius: 20px; vertical-align: middle; margin-left: 10px;">v<?php echo MDQ_VERSION; ?></span></h1>
						<p><?php _e('Convierte tus proyectos en experiencias profesionales inolvidables.', 'porfoliomdq'); ?></p>
					</div>
				</div>

				<div class="mdq-intro">
					<h2 style="font-size: 22px;"><?php _e('¿Qué es Porfolio MDQ?', 'porfoliomdq'); ?></h2>
					<p style="font-size: 16px; line-height: 1.6; color: #444;">
						<?php _e('Este plugin ha sido diseñado para centralizar y profesionalizar la exposición de tus trabajos. No es solo una lista de proyectos; es una herramienta completa con sistema de descargas, galería interactiva optimizada con Lightbox y un motor de proyectos relacionados para mantener a tus visitantes enganchados.', 'porfoliomdq'); ?>
					</p>
				</div>

				<div class="mdq-feature-grid">
					<div class="mdq-feature-item">
						<i class="fas fa-layer-group"></i>
						<h3><?php _e('Layout Profesional', 'porfoliomdq'); ?></h3>
						<p><?php _e('Elige entre un diseño clásico o uno moderno con barra lateral para organizar mejor la información larga y visualización de portada.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-heart"></i>
						<h3><?php _e('Donaciones PayPal', 'porfoliomdq'); ?></h3>
						<p><?php _e('Habilita una sección de donación única por proyecto con mensajes enriquecidos (HTML) y un botón premium centrado.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-images"></i>
						<h3><?php _e('Galería Lightbox', 'porfoliomdq'); ?></h3>
						<p><?php _e('Visualización cinematográfica de tus capturas sin salir de la página del proyecto.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-play-circle"></i>
						<h3><?php _e('Demo en Vivo', 'porfoliomdq'); ?></h3>
						<p><?php _e('Añade botones de demostración con texto e iconos personalizables para que tus clientes prueben tus apps directamente.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-exchange-alt"></i>
						<h3><?php _e('Navegación Smart', 'porfoliomdq'); ?></h3>
						<p><?php _e('Botones Siguiente/Anterior automáticos al final de cada proyecto para mejorar la retención y navegación.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-video"></i>
						<h3><?php _e('Soporte de Video', 'porfoliomdq'); ?></h3>
						<p><?php _e('Integra videos de YouTube o Vimeo directamente en tu galería multimedia con soporte para Lightbox.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-cog"></i>
						<h3><?php _e('Configuración Unificada', 'porfoliomdq'); ?></h3>
						<p><?php _e('Panel de configuración global centralizado para títulos, subtítulos y textos de botones.', 'porfoliomdq'); ?></p>
					</div>
					<div class="mdq-feature-item">
						<i class="fas fa-paint-brush"></i>
						<h3><?php _e('Personalización Visual', 'porfoliomdq'); ?></h3>
						<p><?php _e('Control total sobre el color de fondo y los márgenes de seguridad para adaptar el portafolio a cualquier tema de WordPress.', 'porfoliomdq'); ?></p>
					</div>
				</div>

				<div class="mdq-shortcode-box">
					<h3 style="margin-top:0;"><?php _e('🚀 Modo de Uso Rápido', 'porfoliomdq'); ?></h3>
					<p><?php _e('Puedes mostrar tu portafolio en cualquier página o entrada usando este shortcode:', 'porfoliomdq'); ?></p>
					<code class="mdq-code-display">[porfolio_mdq_view limit="6"]</code>
					<p style="font-size: 14px;"><strong><?php _e('Parámetros disponibles:', 'porfoliomdq'); ?></strong></p>
					<ul style="list-style: disc; margin-left: 20px; font-size: 14px; color: #444;">
						<li><code>limit="6"</code>: <?php _e('Cantidad de proyectos a mostrar.', 'porfoliomdq'); ?></li>
						<li><code>category="slug"</code>: <?php _e('Filtrar por una categoría específica.', 'porfoliomdq'); ?></li>
						<li><code>language="slug"</code>: <?php _e('Filtrar por un lenguaje de programación.', 'porfoliomdq'); ?></li>
						<li><code>title="Mi Título"</code>: <?php _e('Personaliza el título de esta instancia.', 'porfoliomdq'); ?></li>
						<li><code>subtitle="Mi Subtítulo"</code>: <?php _e('Personaliza el subtítulo de esta instancia.', 'porfoliomdq'); ?></li>
					</ul>
				</div>

				<div class="mdq-changelog-section" style="margin-top: 40px; padding-top: 30px; border-top: 1px dashed #e2e8f0;">
					<h3 style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
						<i class="fas fa-history" style="color: #6366f1;"></i> <?php _e('Historial de Cambios', 'porfoliomdq'); ?>
					</h3>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.4.1</strong> - <?php _e('Corrección de diseño: Se ha ajustado el color de fondo predeterminado a blanco para evitar contrastes no deseados y se ha forzado la recarga de activos en producción.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.4.0</strong> - <?php _e('Personalización de Diseño: Se han añadido selectores de color de fondo y controles de márgenes superior/inferior.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.3.3</strong> - <?php _e('Consolidación de Configuración: Se ha eliminado el formulario redundante del Dashboard, centralizando todos los controles en Proyectos > Configuración.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.3.2</strong> - <?php _e('Edición Dinámica de Cabeceras: Ahora puedes editar el título y subtítulo del portafolio globalmente desde Proyectos > Configuración o individualmente en Gutenberg, Elementor y Shortcodes.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.3.0</strong> - <?php _e('Rediseño mayor de la Ficha de Proyecto: Título integrado bajo la imagen, estructura de tarjeta unificada y márgenes de seguridad.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.2.9</strong> - <?php _e('Mejoras visuales: Descripción en tarjeta blanca y etiquetas (pills) para Categorías y Lenguajes.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.2.7</strong> - <?php _e('Botón Demo en Vivo personalizable y visualmente destacado.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.2.6</strong> - <?php _e('Donaciones PayPal con soporte para mensajes enriquecidos. Layout Profesional con Barra Lateral.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.2.5</strong> - <?php _e('Soporte para video interactivo en galerías y Lightbox optimizado.', 'porfoliomdq'); ?>
					</div>
					<div class="mdq-version-note" style="margin-bottom: 20px;">
						<strong style="color: #6366f1;">v1.2.0</strong> - <?php _e('Lanzamiento del Layout Profesional con filtrado AJAX dinámico.', 'porfoliomdq'); ?>
					</div>
				</div>

				<div class="mdq-footer-about">
					&copy; <?php echo date('Y'); ?> <?php _e('Desarrollado por', 'porfoliomdq'); ?> <a href="https://mundodequimeras.com" target="_blank">Mundo de Quimeras (MDQ)</a>
				</div>
			</div>
		</div>
		<?php
	}
}
