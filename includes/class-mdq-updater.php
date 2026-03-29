<?php
/**
 * Clase para manejar las actualizaciones automáticas desde GitHub.
 *
 * @package PorfolioMDQ
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MDQ_Updater {

	private $file;
	private $plugin_slug;
	private $base_url;
	private $repo;
	private $github_response;

	/**
	 * Constructor.
	 *
	 * @param string $file Ruta al archivo principal del plugin.
	 * @param string $repo Repositorio en formato 'usuario/repositorio'.
	 */
	public function __construct( $file, $repo ) {
		$this->file        = $file;
		$this->plugin_slug = plugin_basename( $file );
		$this->repo        = $repo;
		$this->base_url    = "https://api.github.com/repos/{$repo}/releases/latest";

		// Hooks de WordPress para actualizaciones
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
		add_filter( 'plugins_api', array( $this, 'get_plugin_info' ), 20, 3 );
	}

	/**
	 * Consulta la API de GitHub para obtener la última versión.
	 */
	private function get_github_latest_release() {
		if ( ! empty( $this->github_response ) ) {
			return $this->github_response;
		}

		// Intentar obtener desde caché (transiente) por 12 horas
		$transient_name = 'mdq_github_update_' . md5( $this->repo );
		$cached_res     = get_transient( $transient_name );
		if ( $cached_res ) {
			$this->github_response = $cached_res;
			return $cached_res;
		}

		$args = array(
			'timeout' => 15,
			'headers' => array(
				'Accept' => 'application/vnd.github.v3+json',
			),
		);

		$response = wp_remote_get( $this->base_url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		if ( ! empty( $data ) && isset( $data->tag_name ) ) {
			$this->github_response = $data;
			set_transient( $transient_name, $data, 12 * HOUR_IN_SECONDS );
			return $data;
		}

		return false;
	}

	/**
	 * Informa a WordPress sobre la actualización si existe una versión superior en GitHub.
	 */
	public function check_for_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_github_latest_release();
		if ( ! $release ) {
			return $transient;
		}

		// Limpiar la 'v' inicial si existe en el tag_name de GitHub
		$remote_version = ltrim( $release->tag_name, 'v' );
		$local_version  = MDQ_VERSION;

		if ( version_compare( $remote_version, $local_version, '>' ) ) {
			$obj              = new stdClass();
			$obj->slug        = 'porfoliomdq';
			$obj->plugin      = $this->plugin_slug;
			$obj->new_version = $remote_version;
			$obj->url         = $release->html_url;
			$obj->package     = $release->zipball_url; // WordPress descarga el ZIP desde aquí
			$obj->tested      = '6.4'; // Versión máxima probada
			$obj->requires    = '5.8';

			$transient->response[ $this->plugin_slug ] = $obj;
		}

		return $transient;
	}

	/**
	 * Muestra la información detallada (popup) del plugin en el panel de WordPress.
	 */
	public function get_plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( isset( $args->slug ) && $args->slug === 'porfoliomdq' ) {
			$release = $this->get_github_latest_release();
			if ( ! $release ) {
				return $result;
			}

			$res              = new stdClass();
			$res->name        = 'Porfolio MDQ';
			$res->slug        = 'porfoliomdq';
			$res->version     = ltrim( $release->tag_name, 'v' );
			$res->author      = '<a href="https://mundodequimeras.com">Diego Lazo (Mundo de Quimeras)</a>';
			$res->homepage    = 'https://mundodequimeras.com';
			$res->download_link = $release->zipball_url;
			$res->tested      = '6.4';
			$res->requires    = '5.8';
			$res->icons       = array(
				'1x' => 'https://raw.githubusercontent.com/mundodequimeras/porfoliomdq/main/assets/icon.png',
				'2x' => 'https://raw.githubusercontent.com/mundodequimeras/porfoliomdq/main/assets/icon.png',
			);
			$res->banners     = array(
				'low'  => 'https://raw.githubusercontent.com/mundodequimeras/porfoliomdq/main/assets/banner.png',
				'high' => 'https://raw.githubusercontent.com/mundodequimeras/porfoliomdq/main/assets/banner.png',
			);
			$res->sections    = array(
				'description' => 'Un plugin profesional para gestionar el porfolio con categorías, lenguajes e iconos.',
				'changelog'   => $release->body ? nl2br( $release->body ) : 'Sin registro de cambios disponible.',
			);

			return $res;
		}

		return $result;
	}
}
