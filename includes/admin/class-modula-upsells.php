<?php

/**
 * Class Modula Upsells
 */
class Modula_Upsells {
	
	function __construct() {
		
		/* Hooks */
		add_filter( 'modula_general_tab_content', array( $this, 'general_tab_upsell' ) );

	}

	public function general_tab_upsell( $tab_content ) {

		$upsell = '<div class="modula-upsell">';
		$upsell .= '<h2>' . esc_html__( 'Want to make your gallery workflow even better?', 'text-domain' ) . '</h2>';
		$upsell .= '<p>' . esc_html__( 'Suspendisse ex ligula, feugiat tincidunt interdum quis, ornare et erat. In accumsan elit libero, quis scelerisque turpis interdum eu. Nulla rutrum ex non maximus malesuada.', 'text-domain' ) . '</p>';
		$upsell .= '<p><a href="#"  class="info-link button">' . esc_html__( 'See LITE vs PRO Differences', 'text-domain' ) . '</a><a href="#" class="action-link button">' . esc_html__( 'Get Modula Pro!', 'text-domain' ) . '</a></p>';
		$upsell .= '</div>';

		$tab_content .= $upsell;

		return $tab_content;
	}

}

new Modula_Upsells();