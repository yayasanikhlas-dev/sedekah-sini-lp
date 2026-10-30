<?php
/**
 * On-demand compression of local Media Library audio. Never runs on page views.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'media_row_actions', 'sslp_audio_compression_action', 10, 2 );
add_action( 'admin_post_sslp_compress_audio', 'sslp_compress_audio_request' );

function sslp_audio_compression_action( $actions, $post ) {
	if ( wp_attachment_is( 'audio', $post ) && current_user_can( 'upload_files' ) && current_user_can( 'edit_post', $post->ID ) ) {
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=sslp_compress_audio&attachment_id=' . $post->ID ), 'sslp_compress_audio_' . $post->ID );
		$actions['sslp_compress_audio'] = '<a href="' . esc_url( $url ) . '">Compress voice note</a>';
	}
	return $actions;
}

/**
 * Return a smaller MP3 attachment or a descriptive error. Originals are untouched.
 */
function sslp_compress_audio( $attachment_id ) {
	if ( ! wp_attachment_is( 'audio', $attachment_id ) ) {
		return new WP_Error( 'invalid_audio', 'Pilih fail audio dalam Media Library.' );
	}
	$uploads = wp_upload_dir();
	$source  = realpath( get_attached_file( $attachment_id, true ) ?: '' );
	$base    = realpath( $uploads['basedir'] );
	if ( $uploads['error'] || ! $base || ! $source || ! is_file( $source ) || ! is_readable( $source ) || 0 !== strpos( wp_normalize_path( $source ), trailingslashit( wp_normalize_path( $base ) ) ) ) {
		return new WP_Error( 'local_audio_required', 'Fail audio mesti tersedia dalam folder uploads hosting ini.' );
	}
	if ( ! function_exists( 'exec' ) ) {
		return new WP_Error( 'exec_disabled', 'Hosting tidak membenarkan pemampatan audio. Minta pentadbir mengaktifkan exec dan FFmpeg.' );
	}
	$binary = defined( 'SSLP_FFMPEG_PATH' ) ? SSLP_FFMPEG_PATH : 'ffmpeg';
	$check  = array();
	$status = 1;
	exec( escapeshellarg( $binary ) . ' -version 2>&1', $check, $status );
	if ( 0 !== $status ) {
		return new WP_Error( 'ffmpeg_missing', 'FFmpeg tidak tersedia. Minta pentadbir memasang FFmpeg dengan encoder libmp3lame; SSLP_FFMPEG_PATH boleh ditetapkan dalam wp-config.php.' );
	}
	$name   = sanitize_file_name( pathinfo( $source, PATHINFO_FILENAME ) . '-voice-64k.mp3' );
	$target = trailingslashit( $uploads['path'] ) . wp_unique_filename( $uploads['path'], $name );
	$output = array();
	// Local files only, no network protocols/playlists; bounded duration and CPU use.
	$command = escapeshellarg( $binary ) . ' -nostdin -hide_banner -loglevel error -n -protocol_whitelist file -threads 1 -i ' . escapeshellarg( $source ) . ' -map 0:a:0 -vn -map_metadata -1 -t 600 -ac 1 -ar 44100 -c:a libmp3lame -b:a 64k -threads 1 ' . escapeshellarg( $target ) . ' 2>&1';
	// Refuse long recordings rather than silently truncating them.
	$metadata = wp_read_audio_metadata( $source );
	if ( ! $metadata || empty( $metadata['length'] ) || $metadata['length'] > 600 ) {
		return new WP_Error( 'audio_duration', 'Pemampatan menyokong audio sehingga 10 minit dengan tempoh yang boleh dibaca.' );
	}
	exec( $command, $output, $status );
	clearstatcache( true, $target );
	if ( 0 !== $status || ! is_file( $target ) || ! filesize( $target ) ) {
		if ( is_file( $target ) ) {
			wp_delete_file( $target );
		}
		return new WP_Error( 'compression_failed', 'Audio gagal dimampatkan. Semak sokongan FFmpeg/libmp3lame dan kebenaran folder uploads.' );
	}
	if ( filesize( $target ) >= filesize( $source ) ) {
		wp_delete_file( $target );
		return new WP_Error( 'already_small', 'Fail asal sudah lebih kecil atau sama saiz. Fail asal dikekalkan tanpa salinan baharu.' );
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'audio/mpeg',
			'post_title'     => get_the_title( $attachment_id ) . ' (voice 64k)',
			'post_status'    => 'inherit',
		),
		$target,
		0,
		true
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $target );
		return $id;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $target ) );
	update_post_meta( $id, '_sslp_audio_original', $attachment_id );
	return $id;
}

function sslp_compress_audio_request() {
	$id = isset( $_GET['attachment_id'] ) ? absint( $_GET['attachment_id'] ) : 0;
	if ( ! current_user_can( 'upload_files' ) || ! current_user_can( 'edit_post', $id ) ) {
		wp_die( 'Anda tiada kebenaran untuk memampatkan audio ini.', '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'sslp_compress_audio_' . $id );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$result = sslp_compress_audio( $id );
	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ), 'Compress voice note', array( 'back_link' => true ) );
	}
	wp_safe_redirect( admin_url( 'post.php?post=' . $result . '&action=edit' ) );
	exit;
}
