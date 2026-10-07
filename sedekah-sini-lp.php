<?php
/**
 * Plugin Name: Sedekah Sini LP
 * Description: Widget Elementor untuk landing page: karusel logo, tajuk highlight, pemain video, kotak petikan, dan voice note.
 * Version: 1.0.3
 * Author: Sedekah Sini
 * Text Domain: sedekah-sini-lp
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SSLP_VERSION', '1.0.3' );
define( 'SSLP_FILE', __FILE__ );
define( 'SSLP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SSLP_URL', plugin_dir_url( __FILE__ ) );

/**
 * Warn when Elementor is not active.
 */
function sslp_elementor_missing_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>Sedekah Sini LP perlukan plugin Elementor untuk widget landing page.</p></div>';
}

/**
 * Register a panel category in Elementor.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 */
function sslp_register_category( $elements_manager ) {
	$elements_manager->add_category(
		'sedekah-sini',
		array(
			'title' => 'Sedekah Sini LP',
			'icon'  => 'fa fa-plug',
		)
	);
}

/**
 * Register widget styles and scripts.
 */
function sslp_register_assets() {
	wp_register_style(
		'sslp-widgets',
		SSLP_URL . 'assets/css/widgets.css',
		array(),
		SSLP_VERSION
	);

	wp_register_script(
		'sslp-widgets',
		SSLP_URL . 'assets/js/widgets.js',
		array(),
		SSLP_VERSION,
		true
	);
}

/**
 * Register Elementor widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function sslp_register_widgets( $widgets_manager ) {
	require_once SSLP_PATH . 'widgets/class-logo-carousel.php';
	require_once SSLP_PATH . 'widgets/class-highlight-heading.php';
	require_once SSLP_PATH . 'widgets/class-video-player.php';
	require_once SSLP_PATH . 'widgets/class-quote-box.php';
	require_once SSLP_PATH . 'widgets/class-voice-note.php';

	$widgets_manager->register( new SSLP_Widget_Logo_Carousel() );
	$widgets_manager->register( new SSLP_Widget_Highlight_Heading() );
	$widgets_manager->register( new SSLP_Widget_Video_Player() );
	$widgets_manager->register( new SSLP_Widget_Quote_Box() );
	$widgets_manager->register( new SSLP_Widget_Voice_Note() );
}

require_once SSLP_PATH . 'includes/class-updater.php';

/**
 * Boot after plugins load so Elementor classes exist.
 */
function sslp_bootstrap() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'sslp_elementor_missing_notice' );
		return;
	}

	add_action( 'elementor/elements/categories_registered', 'sslp_register_category' );
	add_action( 'elementor/widgets/register', 'sslp_register_widgets' );
	add_action( 'elementor/frontend/after_register_styles', 'sslp_register_assets' );
	add_action( 'elementor/frontend/after_register_scripts', 'sslp_register_assets' );
}
add_action( 'plugins_loaded', 'sslp_bootstrap' );
