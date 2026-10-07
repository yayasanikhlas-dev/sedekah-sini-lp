<?php
/**
 * Heading with inline highlight or custom color.
 *
 * @package SedekahSiniLP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SSLP_Widget_Highlight_Heading extends \Elementor\Widget_Base {
	public function get_name() {
		return 'sslp-highlight-heading';
	}

	public function get_title() {
		return 'Tajuk Highlight';
	}

	public function get_icon() {
		return 'eicon-heading';
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
				'label' => 'Tajuk',
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'   => 'Tag',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'p'  => 'P',
				),
			)
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'   => 'Teks',
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Teks',
			)
		);
		$repeater->add_control(
			'style',
			array(
				'label'   => 'Gaya',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => array(
					'normal'    => 'Biasa',
					'highlight' => 'Highlight',
					'color'     => 'Tukar warna',
				),
			)
		);
		$repeater->add_control(
			'text_color',
			array(
				'label'     => 'Warna teks bahagian ini',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'style' => 'color',
				),
			)
		);

		$this->add_control(
			'parts',
			array(
				'label'       => 'Bahagian ayat',
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array(
						'text'  => 'Bacaan Kolesterol Masih Tinggi Walaupun Dah',
						'style' => 'normal',
					),
					array(
						'text'  => 'Jaga',
						'style' => 'highlight',
					),
					array(
						'text'  => 'Makan &',
						'style' => 'highlight',
					),
					array(
						'text'  => 'Ambil Ubat?',
						'style' => 'highlight',
					),
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

		$this->add_responsive_control(
			'align',
			array(
				'label'     => 'Jajaran',
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => 'Kiri',
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => 'Tengah',
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => 'Kanan',
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .sslp-heading' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .sslp-heading',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => 'Warna teks',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .sslp-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_heading',
			array(
				'label'     => 'Highlight',
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'highlight_bg',
			array(
				'label'     => 'Latar highlight',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#f3e04a',
				'selectors' => array(
					'{{WRAPPER}} .sslp-heading__part--highlight' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_color',
			array(
				'label'     => 'Teks highlight',
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1c2414',
				'selectors' => array(
					'{{WRAPPER}} .sslp-heading__part--highlight' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_radius',
			array(
				'label'      => 'Sudut highlight',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'size' => 4,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .sslp-heading__part--highlight' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$allowed  = array( 'h1', 'h2', 'h3', 'h4', 'p' );
		$tag      = isset( $settings['tag'] ) && in_array( $settings['tag'], $allowed, true ) ? $settings['tag'] : 'h2';
		$parts    = isset( $settings['parts'] ) ? $settings['parts'] : array();

		echo '<' . $tag . ' class="sslp-heading">';
		$index = 0;
		foreach ( $parts as $part ) {
			$text = isset( $part['text'] ) ? $part['text'] : '';
			if ( '' === $text ) {
				continue;
			}
			if ( $index > 0 ) {
				echo ' ';
			}
			$index++;
			$style = isset( $part['style'] ) ? $part['style'] : 'normal';
			$class = 'sslp-heading__part';
			$attr  = '';
			if ( 'highlight' === $style ) {
				$class .= ' sslp-heading__part--highlight';
			} elseif ( 'color' === $style && ! empty( $part['text_color'] ) ) {
				$attr = ' style="color:' . esc_attr( $part['text_color'] ) . '"';
			}
			printf(
				'<span class="%1$s"%2$s>%3$s</span>',
				esc_attr( $class ),
				$attr,
				esc_html( $text )
			);
		}
		echo '</' . $tag . '>';
	}
}
