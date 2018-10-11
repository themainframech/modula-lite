<?php

/**
 * Class Modula Upsells
 */
class Modula_Upsells {
	
	function __construct() {
		
		/* Hooks */
		add_filter( 'modula_general_tab_content', array( $this, 'general_tab_upsell' ) );
		add_filter( 'modula_hover-effect_tab_content', array( $this, 'hovereffects_tab_upsell' ) );
		add_filter( 'modula_image-loaded-effects_tab_content', array( $this, 'loadingeffects_tab_upsell' ) );

		/* Add pro vs lite tab */
		add_filter( 'modula_admin_page_tabs', array( $this, 'pro_vs_lite_tab' ) );

		/* Show pro vs lite tab content */
		add_action( 'modula_admin_tab_provslite', array( $this, 'show_provslite_tab' ) );

	}

	public function generate_upsell_box( $title, $description ) {

		$upsell_box = '<div class="modula-upsell">';
		$upsell_box .= '<h2>' . esc_html( $title ) . '</h2>';
		$upsell_box .= '<p class="modula-upsell-description">' . esc_html( $description ) . '</p>';
		$upsell_box .= '<p>';
		$upsell_box .= '<a href="#"  class="button">' . esc_html__( 'See LITE vs PRO Differences', 'modula-gallery' ) . '</a>';
		$upsell_box .= '<a href="#" class="button-primary button">' . esc_html__( 'Get Modula Pro!', 'modula-gallery' ) . '</a>';
		$upsell_box .= '</p>';
		$upsell_box .= '</div>';

		return $upsell_box;
	}

	public function general_tab_upsell( $tab_content ) {

		$upsell_title       = esc_html__( 'Want to more control & ligthboxes?', 'modula-gallery' );
		$upsell_description = esc_html__( 'By upgrading to Modula PRO you will have 5 more lightboxes and new more customization avaible.', 'modula-gallery' );

		$tab_content .= $this->generate_upsell_box( $upsell_title, $upsell_description );

		return $tab_content;
	}

	public function loadingeffects_tab_upsell( $tab_content ) {

		$upsell_title       = esc_html__( 'You need more flexibility ?', 'modula-gallery' );
		$upsell_description = esc_html__( 'By upgrading to Modula PRO you will have 3 more controls like scale an image, horizontal and vertical slide.', 'modula-gallery' );

		$tab_content .= $this->generate_upsell_box( $upsell_title, $upsell_description );

		return $tab_content;

	}

	public function hovereffects_tab_upsell( $tab_content ) {

		$upsell_title       = esc_html__( 'Need more effects ?', 'modula-gallery' );
		$upsell_description = esc_html__( 'By upgrading to Modula PRO you will have 11 more hover effects.', 'modula-gallery' );

		$tab_content .= $this->generate_upsell_box( $upsell_title, $upsell_description );

		return $tab_content;

	}

	public function pro_vs_lite_tab( $tabs ){

		$tabs['provslite'] = array(
			'label'    => esc_html__( 'PRO vs Lite', 'modula-lite' ),
			'priority' => 20
		);

		return $tabs;

	}

	public function show_provslite_tab() {
		include 'tabs/upsell.php';
	}

}
