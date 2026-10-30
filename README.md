# Sedekah Sini LP

## Compress voice notes

In WordPress, open **Media > Library**, switch to **List view**, and choose
**Compress voice note** on an audio file. The plugin creates a smaller MP3 copy
named `(voice 64k)`. Select that copy in the Elementor Voice Note widget.
Existing widgets keep their original selection until you change it.

Compression uses MP3 at 64 kbps, mono, 44.1 kHz, intended for speech. It is lossy;
the original is retained. If the result is not smaller, no copy is kept.
Only local uploads with readable duration of up to 10 minutes are supported.
Compression runs only when requested by a user with upload and attachment-edit
permissions, never when a visitor opens the landing page.

The hosting server needs PHP `exec` and FFmpeg built with `libmp3lame`.
If FFmpeg is not on PATH, the administrator can add this to `wp-config.php`:

```php
define( 'SSLP_FFMPEG_PATH', '/usr/bin/ffmpeg' );
```

Missing FFmpeg, remote/offloaded files, unsupported audio, and processing failures
produce a message without replacing the original. This feature does not change
browser autoplay restrictions.

## Validation

Run `php tests/audio-compression.php` for isolated compression-flow tests with
WordPress and encoder stubs. These tests do not run an actual encoder. Deployment
should also verify compression and playback with real audio on the target hosting.
