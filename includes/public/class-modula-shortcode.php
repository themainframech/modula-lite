<?php

/**
 * 
 */
class Modula_Shortcode {

	private $loader;
	private $resizer;
	
	function __construct() {

		$this->loader  = new Modula_Template_Loader();
		$this->resizer = new Modula_Image();

		add_shortcode( 'modula', array( $this, 'gallery_shortcode_handler' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_gallery_scripts' ) );
	}

	public function add_gallery_scripts() {
		
		// @todo: minify all css & js for a better optimization.
		wp_register_script( 'modula', MODULA_URL . 'assets/js/jquery.modula.js', array( 'jquery' ), null, true );
		// @todo: move effects to modula style
		wp_register_style( 'modula', MODULA_URL . 'assets/css/modula.css', null, null );
		wp_register_style( 'modula-effects', MODULA_URL . 'assets/css/effects.css', null, null );

		wp_register_script( 'lightbox2_script', MODULA_URL . 'assets/js/lightbox.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'lightbox2_stylesheet', MODULA_URL . 'assets/css/lightbox.min.css' );

	}

	public function gallery_shortcode_handler( $atts ) {
		$default_atts = array(
			'id' => false,
		);

		$atts = wp_parse_args( $atts, $default_atts );

		if ( ! $atts['id'] ) {
			return esc_html__( 'Gallery not found.', 'modula-gallery' );
		}

		$rid = rand( 1, 1000 );
		$gallery_id = 'jtg-' . $atts['id'] . '-' . $rid;

		// Check if is an old Modula post or new.
		$gallery = get_post( $atts['id'] );
		if ( 'modula-gallery' != get_post_type( $gallery ) ) {
			$gallery_posts = get_posts( array(
				'post_type' => 'modula-gallery',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key'     => 'modula-id',
						'value'   => $atts['id'],
						'compare' => '=',
					),
				),
			) );

			if ( empty( $gallery_posts ) ) {
				return esc_html__( 'Gallery not found.', 'modula-gallery' );
			}

			$atts['id'] = $gallery_posts[0]->ID;

		}

		$settings = get_post_meta( $atts['id'], 'modula-settings', true );
		$images   = get_post_meta( $atts['id'], 'modula-images', true );

		if ( empty( $settings ) || empty( $images ) ) {
			return esc_html__( 'Gallery not found.', 'modula-gallery' );
		}

		// Main CSS & JS
		wp_enqueue_style( 'modula' );
		wp_enqueue_style( 'modula-effects' );
		wp_enqueue_script( 'modula' );

		switch ( $settings['lightbox'] ) {
			case "lightbox2":
				wp_enqueue_style( 'lightbox2_stylesheet' );
				wp_enqueue_script( 'lightbox2_script' );
				wp_add_inline_script( 'lightbox2_script', 'jQuery(document).ready(function(){lightbox.option({albumLabel: "' . esc_html__( 'Image %1 of %2', 'modula-gallery' ) . '"});});' );
				break;
			default:
				do_action( 'modula_lighbox_shortcode', $settings['lightbox'] );
				break;
		}

		$template_data = array(
			'gallery_id' => $gallery_id,
			'settings'   => $settings,
			'images'     => $images,
			'loader'     => $this->loader,
			'resizer'     => $this->resizer,
		);
		ob_start();

		echo $this->generate_gallery_css( $gallery_id, $settings );
		$this->loader->set_template_data( $template_data );
    	$this->loader->get_template_part( 'modula', 'gallery' );

    	$html = ob_get_clean();
    	return $html;

	}

	private function generate_gallery_css( $gallery_id, $settings ) {

			$css = "<style>";

			if ( $settings['borderSize'] ) {
				$css .= "#{$gallery_id} .item { border: " . $settings['borderSize'] . "px solid " . $settings['borderColor'] . "; }";
			}

			if ( $settings['borderRadius'] ) {
				$css .= "#{$gallery_id} .item { border-radius: " . $settings['borderRadius'] . "px; }";
			}

			if ( $settings['shadowSize'] ) {
				$css .= "#{$gallery_id} .item { box-shadow: " . $settings['shadowColor'] . " 0px 0px " . $settings['shadowSize'] . "px; }";
			}

			if ( $settings['socialIconColor'] ) {
				$css .= "#{$gallery_id} .item .jtg-social a { color: " . $settings['socialIconColor'] . " }";
			}

			$css .= "#{$gallery_id} .item .caption { background-color: " . $settings['captionColor'] . ";  }";

			$css .= "#{$gallery_id} .item .figc { color: " . $settings['captionColor'] . "; font-size: " . $settings['captionFontSize'] . "px; }";

			$css .= "#{$gallery_id} .item .figc h2.jtg-title {  font-size: " . $settings['titleFontSize'] . "px; }";

			// $css .= "#{$gallery_id} .item { transform: scale(" . $settings['loadedScale'] / 100 . ") translate(" . $settings['loadedHSlide'] . 'px,' . $settings['loadedVSlide'] . "px) rotate(" . $settings['loadedRotate'] . "deg); }";

			$css .= "#{$gallery_id} .items { width:" . $settings['width'] . "; height:" . absint( $settings['height'] ) . "px; }";

			$css .= "#{$gallery_id} .items .figc p.description { color:" . $settings['captionColor'] . "; }";


			if ( strlen( $settings['style'] ) ) {
				$css .= $settings['style'];
			}

			$css .= "</style>\n";

			return $css;

	}

}

new Modula_Shortcode();