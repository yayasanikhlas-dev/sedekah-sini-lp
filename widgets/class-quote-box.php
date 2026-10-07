<?php
/**
 * Quote box widget.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SSLP_Widget_Quote_Box extends \Elementor\Widget_Base {
	public function get_name() {
		return 'sslp-quote';
	}

	public function get_title() {
		return 'Kotak Petikan';
	}

	public function get_icon() {
		return 'eicon-blockquote';
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
				'label' => 'Petikan',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => 'Label atas',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'HALIABLITZ BENTONG | JUS PATI GOLD',
			)
		);

		$this->add_control(
			'quote',
			array(
				'label'   => 'Petikan',
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'Bagi setiap penyakit ada ubatnya. Apabila betul ubatnya, maka sembuhlah ia dengan izin Allah Azza wa Jalla.',
				'rows'    => 4,
			)
		);

		$this->add_control(
			'cite',
			array(
				'label'   => 'Sumber',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Hadis Riwayat Muslim',
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
			'overlap',
			array(
				'label'      => 'Tindih elemen atas',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 36,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote-wrap' => 'margin-top: -{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => 'Latar kad',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#fffdf8',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => 'Warna bingkai',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ead9b0',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => 'Latar label',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#e2b34a',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__eyebrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_color',
			array(
				'label'     => 'Teks label',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#3a2a12',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => 'Warna petikan',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1f4d32',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cite_color',
			array(
				'label'     => 'Warna sumber',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#c4962c',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__cite' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'quote_typography',
				'label'    => 'Tipografi petikan',
				'selector' => '{{WRAPPER}} .sslp-quote__text',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo '<div class="sslp-quote-wrap"><blockquote class="sslp-quote">';
		if ( ! empty( $settings['eyebrow'] ) ) {
			printf( '<p class="sslp-quote__eyebrow">%s</p>', esc_html( $settings['eyebrow'] ) );
		}
		if ( ! empty( $settings['quote'] ) ) {
			printf( '<p class="sslp-quote__text">“%s”</p>', esc_html( $settings['quote'] ) );
		}
		if ( ! empty( $settings['cite'] ) ) {
			printf( '<footer class="sslp-quote__cite">%s</footer>', esc_html( $settings['cite'] ) );
		}
		echo '</blockquote></div>';
	}
}
