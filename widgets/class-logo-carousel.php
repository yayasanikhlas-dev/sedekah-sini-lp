<?php
/**
 * Infinite logo carousel widget.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SSLP_Widget_Logo_Carousel extends \Elementor\Widget_Base {
	public function get_name() {
		return 'sslp-logo-carousel';
	}

	public function get_title() {
		return 'Karusel Logo';
	}

	public function get_icon() {
		return 'eicon-carousel';
	}

	public function get_categories() {
		return array( 'sedekah-sini' );
	}

	public function get_style_depends() {
		return array( 'sslp-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => 'Logo',
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'image',
			array(
				'label' => 'Logo',
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'name',
			array(
				'label'       => 'Nama',
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Logo',
				'description' => 'Dipakai sebagai teks alt. Ini yang dibaca enjin carian.',
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'       => 'Pautan',
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://',
				'options'     => array( 'url', 'is_external', 'nofollow' ),
				'default'     => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => false,
				),
			)
		);

		$this->add_control(
			'logos',
			array(
				'label'       => 'Senarai logo',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => 'Arah',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => 'Ke kiri',
					'right' => 'Ke kanan',
				),
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
			'height',
			array(
				'label'      => 'Tinggi logo',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 160,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'gap',
			array(
				'label'      => 'Jarak',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => 'Laju (saat untuk satu pusingan)',
				'type'    => \Elementor\Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min' => 8,
						'max' => 80,
					),
				),
				'default' => array(
					'size' => 28,
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$logos    = array();

		if ( ! empty( $settings['logos'] ) ) {
			foreach ( $settings['logos'] as $logo ) {
				$url = isset( $logo['image']['url'] ) ? $logo['image']['url'] : '';
				if ( ! $url ) {
					continue;
				}
				$logos[] = array(
					'url'  => $url,
					'alt'  => isset( $logo['name'] ) ? $logo['name'] : '',
					'link' => ( isset( $logo['link'] ) && is_array( $logo['link'] ) ) ? $logo['link'] : array(),
				);
			}
		}

		if ( ! $logos ) {
			echo '<p class="sslp-empty">Tambah logo dalam panel widget.</p>';
			return;
		}

		$height = isset( $settings['height']['size'] ) ? (float) $settings['height']['size'] : 48;
		$gap    = isset( $settings['gap']['size'] ) ? (float) $settings['gap']['size'] : 48;
		$speed  = isset( $settings['speed']['size'] ) ? (float) $settings['speed']['size'] : 28;
		$dir    = ( isset( $settings['direction'] ) && 'right' === $settings['direction'] ) ? 'right' : 'left';

		printf(
			'<div class="sslp-logos sslp-logos--%1$s" style="--sslp-logo-height:%2$spx;--sslp-logo-gap:%3$spx;--sslp-logo-speed:%4$ss;">',
			esc_attr( $dir ),
			esc_attr( $height ),
			esc_attr( $gap ),
			esc_attr( $speed )
		);
		echo '<div class="sslp-logos__track">';
		foreach ( array( false, true ) as $clone ) {
			echo $clone ? '<div class="sslp-logos__set" aria-hidden="true">' : '<div class="sslp-logos__set">';
			foreach ( $logos as $logo ) {
				echo $this->logo_html( $logo, ! $clone );
			}
			echo '</div>';
		}
		echo '</div></div>';
	}

	/**
	 * One logo. The moving copy stays an image so crawlers see each link once.
	 *
	 * @param array $logo   Logo data.
	 * @param bool  $linked Whether this copy may contain the real link.
	 * @return string
	 */
	private function logo_html( $logo, $linked ) {
		$img = sprintf(
			'<img src="%1$s" alt="%2$s" />',
			esc_url( $logo['url'] ),
			esc_attr( $linked ? $logo['alt'] : '' )
		);

		$href = ( $linked && isset( $logo['link']['url'] ) ) ? $logo['link']['url'] : '';
		if ( ! $href ) {
			return $img;
		}

		$rel    = array();
		$target = '';
		if ( ! empty( $logo['link']['is_external'] ) ) {
			$target = ' target="_blank"';
			$rel[]  = 'noopener';
			$rel[]  = 'noreferrer';
		}
		if ( ! empty( $logo['link']['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		$rel_attr = $rel ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '';

		return sprintf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $href ),
			$target,
			$rel_attr,
			$img
		);
	}
}
