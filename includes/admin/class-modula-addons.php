<?php

class Modula_Addons {

	private $addons;
	private $upgrade_url = '#';
	
	function __construct() {
		
		$this->addons = $this->check_for_addons();

	}

	private function check_for_addons() {

		// Get the transient where the addons are stored on-site.
	    $data = get_transient( 'modula_addons' );

	    if ( $data ) {
	    	return $data;
	    }

	    // Make sure this matches the exact URL from your site.
	    $url = apply_filters( "modula_addon_server_url", 'http://test.avianstudio.com/wp-json/mt/v1/addons' );

	    // Get data from the remote URL.
	    $response = wp_remote_get( $url );

	    if ( ! is_wp_error( $response ) ) {

	        // Decode the data that we got.
	        $data = json_decode( wp_remote_retrieve_body( $response ) );

	        if ( ! empty( $data ) && is_array( $data ) ) {

	            // Store the data for a week.
	            set_transient( 'modula_addons', $data, 7 * DAY_IN_SECONDS );

	            return $data;
	        }
	    }

	    return array();

	}

	public function render_addons() {

		if ( ! empty( $this->addons ) ) {
			foreach ( $this->addons as $addon ) {
				$image = ( '' != $addon->image ) ? $addon->image : MODULA_URL . 'assets/images/modula-logo.jpg';
				echo '<div class="modula-addon">';
				echo '<div class="modula-addon-box">';
				echo '<img src="' . esc_attr( $image ) . '">';
				echo '<div class="modula-addon-content">';
				echo '<h3>' . esc_html( $addon->name ) . '</h3>';
				echo '<div class="modula-addon-description">' . wp_kses_post( $addon->description ) . '</div>';
				echo '</div>';
				echo '</div>';
				echo '<div class="modula-addon-actions">';
				echo apply_filters( "modula_addon_button_action", '<a href="' . $this->upgrade_url . '" target="_blank" class="button primary-button">' . esc_html__( 'Upgrade to PRO', 'modula-gallery' ) . '</a>', $addon );
				echo '</div>';
				echo '</div>';
			}
		}

		if ( apply_filters( 'modula-show-feature-request', true ) ) {
			echo '<div class="modula-addon">';
			echo '<div class="modula-addon-box">';
			echo '<img src="https://ps.w.org/akismet/assets/icon-256x256.png?rev=969272">';
			echo '<div class="modula-addon-content">';
			echo '<h3>' . esc_html__( 'Feature Request', 'modula-gallery' ) . '</h3>';
			echo '<div class="modula-addon-description">' . esc_html__( 'Don\'t see what you’re looking for? Let us help you build it by making a suggestion!', 'modula-gallery' ) . '</div>';
			echo '</div>';
			echo '</div>';
			echo '<div class="modula-addon-actions">';
			echo '<a href="#" class="button primary-button" target="_blank">' . esc_html__( 'Send Feature Request', 'modula-gallery' ) . '</a>';
			echo '</div>';
			echo '</div>';
		}

	}

}