<?php
/**
 * Video player with optional phone frame.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SSLP_Widget_Video_Player extends \Elementor\Widget_Base {
	public function get_name() {
		return 'sslp-video';
	}

	public function get_title() {
		return 'Pemain Video';
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return array( 'sedekah-sini' );
	}

	public function get_style_depends() {
		return array( 'sslp-widgets' );
	}

	public function get_script_depends() {
		return array( 'sslp-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => 'Video',
			)
		);

		$this->add_control(
			'video',
			array(
				'label'       => 'Fail video',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => array( 'video' ),
			)
		);

		$this->add_control(
			'poster',
			array(
				'label' => 'Gambar muka',
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'badge',
			array(
				'label'       => 'Label atas video',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'Formula Bakar 150ml',
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'        => 'Bingkai telefon',
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => 'Ya',
				'label_off'    => 'Tidak',
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => 'Gaya',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 180,
						'max' => 480,
					),
					'%'  => array(
						'min' => 40,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 320,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-video' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'label'    => 'Tipografi lencana',
				'selector' => '{{WRAPPER}} .sslp-video__badge',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$src      = isset( $settings['video']['url'] ) ? $settings['video']['url'] : '';
		$poster   = isset( $settings['poster']['url'] ) ? $settings['poster']['url'] : '';
		$badge    = isset( $settings['badge'] ) ? $settings['badge'] : '';
		$phone    = ( isset( $settings['phone'] ) && 'yes' === $settings['phone'] );

		if ( ! $src ) {
			echo '<p class="sslp-empty">Muat naik fail video dalam panel widget.</p>';
			return;
		}

		$classes = 'sslp-video' . ( $phone ? ' sslp-video--phone' : '' );
		echo '<div class="' . esc_attr( $classes ) . '">';
		printf(
			'<video playsinline preload="metadata" poster="%1$s" src="%2$s"></video>',
			esc_url( $poster ),
			esc_url( $src )
		);
		if ( $badge ) {
			printf( '<span class="sslp-video__badge">%s</span>', esc_html( $badge ) );
		}
		echo '<button type="button" class="sslp-video__play" aria-label="Main video"><span></span></button>';
		echo '</div>';
	}
}
