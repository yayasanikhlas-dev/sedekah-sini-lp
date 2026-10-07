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
	if ( is_array( $cached ) ) {
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
		set_site_transient( 'sslp_remote_release', $empty, HOUR_IN_SECONDS );
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

	$payload = ( $version && $package ) ? array(
		'version' => $version,
		'package' => $package,
	) : $empty;

	set_site_transient( 'sslp_remote_release', $payload, 6 * HOUR_IN_SECONDS );
	return $payload;
}

/**
 * Add this plugin to the WordPress update list.
 *
 * @param object $transient Update transient.
 * @return object
 */
function sslp_inject_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}

	$plugin  = plugin_basename( SSLP_FILE );
	$remote  = sslp_remote_release();
	$current = array(
		'id'           => $plugin,
		'slug'         => 'sedekah-sini-lp',
		'plugin'       => $plugin,
		'new_version'  => SSLP_VERSION,
		'url'          => 'https://github.com/yayasanikhlas-dev/sedekah-sini-lp',
		'package'      => '',
		'requires'     => '6.0',
		'requires_php' => '7.4',
		'tested'       => '6.8',
	);

	if ( $remote['version'] && $remote['package'] && version_compare( SSLP_VERSION, $remote['version'], '<' ) ) {
		$current['new_version'] = $remote['version'];
		$current['package']     = $remote['package'];
		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}
		$transient->response[ $plugin ] = (object) $current;
	} else {
		if ( isset( $transient->response[ $plugin ] ) ) {
			unset( $transient->response[ $plugin ] );
		}
		if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
			$transient->no_update = array();
		}
		$transient->no_update[ $plugin ] = (object) $current;
	}

	return $transient;
}
add_filter( 'site_transient_update_plugins', 'sslp_inject_update' );

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
	);

	return $info;
}
add_filter( 'plugins_api', 'sslp_plugin_info', 20, 3 );

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
 * Forget the cached release after an update.
 *
 * @param WP_Upgrader $upgrader Upgrader instance.
 * @param array       $options  Upgrade context.
 */
function sslp_clear_update_cache( $upgrader, $options ) {
	if ( empty( $options['plugins'] ) || ! is_array( $options['plugins'] ) ) {
		return;
	}
	if ( in_array( plugin_basename( SSLP_FILE ), $options['plugins'], true ) ) {
		delete_site_transient( 'sslp_remote_release' );
	}
}
add_action( 'upgrader_process_complete', 'sslp_clear_update_cache', 10, 2 );
