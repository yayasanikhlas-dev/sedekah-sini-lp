<?php
/**
 * Check GitHub releases and offer updates inside WordPress.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Latest public release for this plugin.
 *
 * @return array{version:string,package:string}
 */
function sslp_remote_release() {
	$cached = get_site_transient( 'sslp_remote_release' );
	if ( is_array( $cached ) && isset( $cached['version'], $cached['package'] ) ) {
		return $cached;
	}

	$empty    = array(
		'version' => '',
		'package' => '',
	);
	$response = wp_remote_get(
		'https://api.github.com/repos/yayasanikhlas-dev/sedekah-sini-lp/releases/latest',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'SedekahSiniLP-Updater',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		set_site_transient( 'sslp_remote_release', $empty, 15 * MINUTE_IN_SECONDS );
		return $empty;
	}

	$data    = json_decode( wp_remote_retrieve_body( $response ), true );
	$version = '';
	$package = '';

	if ( is_array( $data ) ) {
		$version = isset( $data['tag_name'] ) ? ltrim( (string) $data['tag_name'], 'vV' ) : '';
		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				if ( isset( $asset['name'], $asset['browser_download_url'] ) && 'sedekah-sini-lp.zip' === $asset['name'] ) {
					$package = (string) $asset['browser_download_url'];
					break;
				}
			}
		}
	}

	if ( $version && ! $package ) {
		$package = 'https://github.com/yayasanikhlas-dev/sedekah-sini-lp/releases/download/v' . rawurlencode( $version ) . '/sedekah-sini-lp.zip';
	}

	$payload = ( $version && $package ) ? array(
		'version' => $version,
		'package' => $package,
	) : $empty;

	set_site_transient( 'sslp_remote_release', $payload, 15 * MINUTE_IN_SECONDS );
	return $payload;
}

/**
 * Update payload WordPress stores for this plugin.
 *
 * @param string $version Release version.
 * @param string $package Zip URL.
 * @return object
 */
function sslp_update_payload( $version, $package ) {
	return (object) array(
		'id'           => 'https://github.com/yayasanikhlas-dev/sedekah-sini-lp',
		'slug'         => 'sedekah-sini-lp',
		'plugin'       => plugin_basename( SSLP_FILE ),
		'version'      => $version,
		'new_version'  => $version,
		'url'          => 'https://github.com/yayasanikhlas-dev/sedekah-sini-lp',
		'package'      => $package,
		'requires'     => '6.0',
		'requires_php' => '7.4',
		'tested'       => '6.8',
	);
}

/**
 * Official WordPress 5.8+ update hook for the Update URI header.
 *
 * @param array|false $update      Existing update data.
 * @param array       $plugin_data Plugin headers.
 * @param string      $plugin_file Plugin basename.
 * @return array|false
 */
function sslp_github_update( $update, $plugin_data, $plugin_file ) {
	unset( $plugin_data );
	if ( plugin_basename( SSLP_FILE ) !== $plugin_file ) {
		return $update;
	}

	$remote = sslp_remote_release();
	if ( ! $remote['version'] || ! $remote['package'] ) {
		return $update;
	}

	return (array) sslp_update_payload( $remote['version'], $remote['package'] );
}
add_filter( 'update_plugins_github.com', 'sslp_github_update', 10, 3 );

/**
 * Keep this plugin in the update list even between WordPress.org checks.
 *
 * @param mixed $transient Update transient.
 * @return mixed
 */
function sslp_inject_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}

	$plugin = plugin_basename( SSLP_FILE );
	$remote = sslp_remote_release();

	if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
		$transient->response = array();
	}
	if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
		$transient->no_update = array();
	}
	if ( ! isset( $transient->checked ) || ! is_array( $transient->checked ) ) {
		$transient->checked = array();
	}

	$transient->checked[ $plugin ] = SSLP_VERSION;
	unset( $transient->response[ $plugin ], $transient->no_update[ $plugin ] );

	if ( $remote['version'] && $remote['package'] && version_compare( SSLP_VERSION, $remote['version'], '<' ) ) {
		$transient->response[ $plugin ] = sslp_update_payload( $remote['version'], $remote['package'] );
	} else {
		$transient->no_update[ $plugin ] = sslp_update_payload( $remote['version'] ? $remote['version'] : SSLP_VERSION, $remote['package'] );
	}

	return $transient;
}
add_filter( 'site_transient_update_plugins', 'sslp_inject_update' );
add_filter( 'pre_set_site_transient_update_plugins', 'sslp_inject_update' );

/**
 * Details shown on the plugin update screen.
 *
 * @param false|object|array $result Existing result.
 * @param string             $action Requested action.
 * @param object             $args   Request arguments.
 * @return false|object|array
 */
function sslp_plugin_info( $result, $action, $args ) {
	if ( 'plugin_information' !== $action ) {
		return $result;
	}

	$slug = '';
	if ( is_object( $args ) && isset( $args->slug ) ) {
		$slug = $args->slug;
	}
	if ( 'sedekah-sini-lp' !== $slug ) {
		return $result;
	}

	$remote = sslp_remote_release();
	$info   = new stdClass();

	$info->name          = 'Sedekah Sini LP';
	$info->slug          = 'sedekah-sini-lp';
	$info->version       = $remote['version'] ? $remote['version'] : SSLP_VERSION;
	$info->author        = 'Sedekah Sini';
	$info->homepage      = 'https://github.com/yayasanikhlas-dev/sedekah-sini-lp';
	$info->download_link = $remote['package'];
	$info->requires      = '6.0';
	$info->tested        = '6.8';
	$info->requires_php  = '7.4';
	$info->sections      = array(
		'description' => 'Widget Elementor untuk landing page Sedekah Sini.',
		'changelog'   => '<p>' . esc_html( $info->version ) . '</p>',
	);

	return $info;
}
add_filter( 'plugins_api', 'sslp_plugin_info', 20, 3 );

/**
 * Ask WordPress to look for updates again after this version is installed.
 */
function sslp_bust_update_check() {
	if ( get_option( 'sslp_update_bust' ) === SSLP_VERSION ) {
		return;
	}

	delete_site_transient( 'sslp_remote_release' );
	delete_site_transient( 'update_plugins' );
	update_option( 'sslp_update_bust', SSLP_VERSION, false );
}
add_action( 'admin_init', 'sslp_bust_update_check', 1 );

/**
 * Turn on WordPress auto-updates for this plugin once.
 */
function sslp_enable_auto_update() {
	if ( get_option( 'sslp_auto_update_opt_in' ) ) {
		return;
	}

	$plugin = plugin_basename( SSLP_FILE );
	$auto   = (array) get_site_option( 'auto_update_plugins', array() );
	if ( ! in_array( $plugin, $auto, true ) ) {
		$auto[] = $plugin;
		update_site_option( 'auto_update_plugins', $auto );
	}

	update_option( 'sslp_auto_update_opt_in', 1, false );
}
add_action( 'admin_init', 'sslp_enable_auto_update' );

/**
 * Show a clear update notice on the Plugins screen.
 */
function sslp_update_admin_notice() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'plugins', 'update-core' ), true ) ) {
		return;
	}

	$remote = sslp_remote_release();
	if ( ! $remote['version'] || ! version_compare( SSLP_VERSION, $remote['version'], '<' ) ) {
		return;
	}

	$plugin = plugin_basename( SSLP_FILE );
	$url    = wp_nonce_url(
		self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $plugin ) ),
		'upgrade-plugin_' . $plugin
	);

	echo '<div class="notice notice-warning"><p>';
	echo esc_html( sprintf( 'Sedekah Sini LP %s tersedia. Versi yang dipasang ialah %s.', $remote['version'], SSLP_VERSION ) );
	echo ' <a href="' . esc_url( $url ) . '">Kemas kini sekarang</a>';
	echo '</p></div>';
}
add_action( 'admin_notices', 'sslp_update_admin_notice' );

/**
 * Forget the cached release after an update.
 *
 * @param WP_Upgrader $upgrader Upgrader instance.
 * @param array       $options  Upgrade context.
 */
function sslp_clear_update_cache( $upgrader, $options ) {
	unset( $upgrader );
	if ( empty( $options['plugins'] ) || ! is_array( $options['plugins'] ) ) {
		return;
	}
	if ( in_array( plugin_basename( SSLP_FILE ), $options['plugins'], true ) ) {
		delete_site_transient( 'sslp_remote_release' );
	}
}
add_action( 'upgrader_process_complete', 'sslp_clear_update_cache', 10, 2 );
