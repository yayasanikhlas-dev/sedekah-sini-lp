<?php
/**
 * Voice-note style audio player.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SSLP_Widget_Voice_Note extends \Elementor\Widget_Base {
	public function get_name() {
		return 'sslp-voice-note';
	}

	public function get_title() {
		return 'Voice Note';
	}

	public function get_icon() {
		return 'eicon-headphones';
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
				'label' => 'Audio',
			)
		);

		$this->add_control(
			'audio',
			array(
				'label'       => 'Fail audio',
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'media_types' => array( 'audio' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => 'Tajuk',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Dengar pesanan kami',
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

		$this->add_control(
			'pill_bg',
			array(
				'label'     => 'Latar',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(12, 48, 28, 0.35)',
				'selectors' => array(
					'{{WRAPPER}} .sslp-voice' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_border',
			array(
				'label'     => 'Bingkai',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#e6c56a',
				'selectors' => array(
					'{{WRAPPER}} .sslp-voice' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pill_color',
			array(
				'label'     => 'Teks',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .sslp-voice' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => 'Tipografi tajuk',
				'selector' => '{{WRAPPER}} .sslp-voice__title',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'time_typography',
				'label'    => 'Tipografi masa',
				'selector' => '{{WRAPPER}} .sslp-voice__time',
			)
		);

		$this->add_control(
			'play_bg',
			array(
				'label'     => 'Butang main',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#f5d000',
				'selectors' => array(
					'{{WRAPPER}} .sslp-voice__play' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => 'Jajaran',
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => 'Kiri',
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => 'Tengah',
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => 'Kanan',
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => 'display: flex; justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$src      = isset( $settings['audio']['url'] ) ? $settings['audio']['url'] : '';
		$title    = isset( $settings['title'] ) ? $settings['title'] : '';

		if ( ! $src ) {
			echo '<p class="sslp-empty">Muat naik fail audio dalam panel widget.</p>';
			return;
		}

		echo '<div class="sslp-voice" data-sslp-voice>';
		echo '<button type="button" class="sslp-voice__play" aria-label="Main audio"></button>';
		echo '<span class="sslp-voice__body">';
		printf( '<span class="sslp-voice__title">%s</span>', esc_html( $title ) );
		echo '<span class="sslp-voice__track"><span class="sslp-voice__fill"></span></span>';
		echo '</span>';
		echo '<span class="sslp-voice__time">0:00</span>';
		printf( '<audio preload="metadata" src="%s"></audio>', esc_url( $src ) );
		echo '</div>';
	}
}
