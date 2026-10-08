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
			'layout',
			array(
				'label'       => 'Jenis',
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'default'     => 'quote',
				'options'     => array(
					'quote' => array(
						'title' => 'Petikan',
						'icon'  => 'eicon-blockquote',
					),
					'dalil' => array(
						'title' => 'Dalil',
						'icon'  => 'eicon-t-letter',
					),
				),
				'description' => 'Dalil keluarkan dua teks: ayat Arab, kemudian makna.',
				'toggle'      => false,
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
				'label'       => 'Teks 1',
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => 'Bagi setiap penyakit ada ubatnya. Apabila betul ubatnya, maka sembuhlah ia dengan izin Allah Azza wa Jalla.',
				'rows'        => 4,
				'description' => 'Untuk dalil, ini ayat Arab.',
			)
		);

		$this->add_control(
			'meaning',
			array(
				'label'       => 'Teks 2',
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'rows'        => 3,
				'placeholder' => 'Makna ayat',
				'description' => 'Teks kedua. Untuk dalil, ini makna.',
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
			'style_card',
			array(
				'label' => 'Kad',
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

		$this->add_responsive_control(
			'card_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'px' => array(
						'min' => 180,
						'max' => 1400,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_eyebrow',
			array(
				'label' => 'Label',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
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

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'label'    => 'Tipografi label',
				'selector' => '{{WRAPPER}} .sslp-quote__eyebrow',
			)
		);

		$this->add_responsive_control(
			'eyebrow_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 80,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__eyebrow' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'eyebrow_height',
			array(
				'label'      => 'Tinggi kotak',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__eyebrow' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'eyebrow_padding',
			array(
				'label'      => 'Ruang dalam kotak',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '8',
					'right'    => '18',
					'bottom'   => '8',
					'left'     => '18',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__eyebrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_quote',
			array(
				'label' => 'Petikan',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
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

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'quote_typography',
				'label'    => 'Tipografi petikan',
				'selector' => '{{WRAPPER}} .sslp-quote__text',
			)
		);

		$this->add_responsive_control(
			'quote_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'px' => array(
						'min' => 120,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__text' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_arabic',
			array(
				'label' => 'Ayat dalil',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'arabic_color',
			array(
				'label'     => 'Warna ayat',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1f4d32',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__arabic' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'arabic_typography',
				'label'    => 'Tipografi ayat',
				'selector' => '{{WRAPPER}} .sslp-quote__arabic',
			)
		);

		$this->add_responsive_control(
			'arabic_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'px' => array(
						'min' => 120,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__arabic' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_meaning',
			array(
				'label' => 'Makna',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'meaning_color',
			array(
				'label'     => 'Warna makna',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1f4d32',
				'selectors' => array(
					'{{WRAPPER}} .sslp-quote__meaning' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'meaning_typography',
				'label'    => 'Tipografi makna',
				'selector' => '{{WRAPPER}} .sslp-quote__meaning',
			)
		);

		$this->add_responsive_control(
			'meaning_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'px' => array(
						'min' => 120,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__meaning' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_cite',
			array(
				'label' => 'Sumber',
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
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
				'name'     => 'cite_typography',
				'label'    => 'Tipografi sumber',
				'selector' => '{{WRAPPER}} .sslp-quote__cite',
			)
		);

		$this->add_responsive_control(
			'cite_width',
			array(
				'label'      => 'Lebar',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 80,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-quote__cite' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$dalil    = isset( $settings['layout'] ) && 'dalil' === $settings['layout'];
		$quote    = isset( $settings['quote'] ) ? trim( $settings['quote'] ) : '';
		$meaning  = isset( $settings['meaning'] ) ? trim( $settings['meaning'] ) : '';
		$arabic   = isset( $settings['arabic'] ) ? trim( $settings['arabic'] ) : '';

		if ( $dalil && '' !== $arabic ) {
			$quote = $arabic;
		}

		echo '<div class="sslp-quote-wrap"><blockquote class="sslp-quote">';
		if ( ! empty( $settings['eyebrow'] ) ) {
			printf( '<p class="sslp-quote__eyebrow">%s</p>', esc_html( $settings['eyebrow'] ) );
		}
		if ( '' !== $quote ) {
			if ( $dalil ) {
				printf(
					'<p class="sslp-quote__arabic" lang="ar" dir="rtl">%s</p>',
					esc_html( $quote )
				);
			} else {
				printf( '<p class="sslp-quote__text">“%s”</p>', esc_html( $quote ) );
			}
		}
		if ( '' !== $meaning ) {
			printf( '<p class="sslp-quote__meaning">“%s”</p>', esc_html( $meaning ) );
		}
		if ( ! empty( $settings['cite'] ) ) {
			printf( '<footer class="sslp-quote__cite">%s</footer>', esc_html( $settings['cite'] ) );
		}
		echo '</blockquote></div>';
	}

	protected function content_template() {
		?>
		<#
		var dalil = settings.layout === 'dalil';
		var quote = settings.quote || '';
		var meaning = settings.meaning || '';
		if ( dalil && settings.arabic ) {
			quote = settings.arabic;
		}
		#>
		<div class="sslp-quote-wrap"><blockquote class="sslp-quote">
			<# if ( settings.eyebrow ) { #>
				<p class="sslp-quote__eyebrow">{{{ settings.eyebrow }}}</p>
			<# } #>
			<# if ( quote ) { #>
				<# if ( dalil ) { #>
					<p class="sslp-quote__arabic" lang="ar" dir="rtl">{{{ quote }}}</p>
				<# } else { #>
					<p class="sslp-quote__text">“{{{ quote }}}”</p>
				<# } #>
			<# } #>
			<# if ( meaning ) { #>
				<p class="sslp-quote__meaning">“{{{ meaning }}}”</p>
			<# } #>
			<# if ( settings.cite ) { #>
				<footer class="sslp-quote__cite">{{{ settings.cite }}}</footer>
			<# } #>
		</blockquote></div>
		<?php
	}
}
