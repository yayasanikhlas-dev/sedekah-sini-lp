<?php
/** Run with php tests/audio-compression.php; no WordPress/FFmpeg required. */
namespace SSLPCompressionTest;

define( 'ABSPATH', __DIR__ );
class WP_Error {
	public $code;
	public function __construct( $code, $message = '' ) { $this->code = $code; }
}
function add_filter() {}
function add_action() {}
function wp_attachment_is() { return $GLOBALS['audio']; }
function wp_upload_dir() { return array( 'basedir' => $GLOBALS['dir'], 'path' => $GLOBALS['dir'], 'error' => false ); }
function get_attached_file() { return $GLOBALS['source']; }
function wp_normalize_path( $path ) { return str_replace( '\\', '/', $path ); }
function trailingslashit( $path ) { return rtrim( $path, '/\\' ) . '/'; }
function sanitize_file_name( $name ) { return $name; }
function wp_unique_filename( $dir, $name ) { return $name; }
function wp_read_audio_metadata() { return array( 'length' => $GLOBALS['duration'] ); }
function wp_delete_file( $file ) { unlink( $file ); }
function wp_insert_attachment() { return $GLOBALS['insert']; }
function get_the_title() { return 'Test'; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_update_attachment_metadata() {}
function wp_generate_attachment_metadata() { return array(); }
function update_post_meta() {}
function exec( $command, &$output, &$status ) {
	if ( strpos( $command, ' -version' ) !== false ) {
		$status = $GLOBALS['ffmpeg_status'];
		return;
	}
	$GLOBALS['command'] = $command;
	$status = $GLOBALS['encode_status'];
	file_put_contents( $GLOBALS['target'], str_repeat( 'x', $GLOBALS['encoded_size'] ) );
}

$dir = sys_get_temp_dir() . '/sslp-compress-' . uniqid();
mkdir( $dir );
$source = $dir . '/source.wav';
$target = $dir . '/source-voice-64k.mp3';
file_put_contents( $source, str_repeat( 'x', 1024 ) );
$audio = true;
$duration = 60;
$ffmpeg_status = 0;
$encode_status = 0;
$encoded_size = 256;
$insert = 42;
$code = file_get_contents( __DIR__ . '/../includes/audio-compression.php' );
eval( 'namespace SSLPCompressionTest;' . substr( $code, 5 ) );
function expect( $condition, $message ) {
	if ( ! $condition ) { throw new \RuntimeException( $message ); }
}
try {
	$audio = false;
	expect( sslp_compress_audio( 1 )->code === 'invalid_audio', 'Reject non-audio' );
	$audio = true;
	$original = $source;
	$source = __FILE__;
	expect( sslp_compress_audio( 1 )->code === 'local_audio_required', 'Reject files outside uploads' );
	$source = $original;
	$ffmpeg_status = 1;
	expect( sslp_compress_audio( 1 )->code === 'ffmpeg_missing', 'Missing encoder' );
	$ffmpeg_status = 0;
	$duration = 601;
	expect( sslp_compress_audio( 1 )->code === 'audio_duration', 'Reject long audio' );
	$duration = 60;
	$encode_status = 1;
	expect( sslp_compress_audio( 1 )->code === 'compression_failed' && ! file_exists( $target ), 'Clean failed output' );
	$encode_status = 0;
	$encoded_size = 2048;
	expect( sslp_compress_audio( 1 )->code === 'already_small' && ! file_exists( $target ), 'Reject larger output' );
	$encoded_size = 256;
	$insert = new WP_Error( 'insert_failed' );
	expect( sslp_compress_audio( 1 )->code === 'insert_failed' && ! file_exists( $target ), 'Clean insert failure' );
	$insert = 42;
	expect( sslp_compress_audio( 1 ) === 42 && filesize( $target ) === 256, 'Register compressed copy' );
	expect( filesize( $source ) === 1024, 'Preserve original' );
	expect( strpos( $command, '-b:a 64k' ) !== false && strpos( $command, '-ac 1' ) !== false, 'Voice encoding settings' );
	echo "PASS: invalid audio, local path boundary, missing FFmpeg, duration limit, failed output cleanup, size comparison, insert failure, success, original preserved, encoder options.\n";
} finally {
	if ( is_file( $target ) ) { unlink( $target ); }
	unlink( $source );
	rmdir( $dir );
}
