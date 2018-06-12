<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 */
class Modula {
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once MODULA_PATH . 'includes/libraries/class-modula-template-loader.php';
		require_once MODULA_PATH . 'includes/helper/class-modula-helper.php';
		require_once MODULA_PATH . 'includes/admin/class-modula-image.php';

		require_once MODULA_PATH . 'includes/admin/class-modula-cpt.php';
		require_once MODULA_PATH . 'includes/admin/class-modula-upsells.php';

		require_once MODULA_PATH . 'includes/public/class-modula-shortcode.php';

	}

	private function set_locale() {
		
	}

	private function define_admin_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		new Modula_CPT();

	}

	private function define_public_hooks() {
		
	}

	/* Enqueue Admin Scripts */
	public function admin_scripts( $hook ) {

		global $id, $post;

        // Get current screen.
        $screen = get_current_screen();

        // Check if is modula custom post type
        if ( 'modula-gallery' !== $screen->post_type ) {
            return;
        }

        // Set the post_id
        $post_id = isset( $post->ID ) ? $post->ID : (int) $id;

		if ( 'post-new.php' == $hook || 'post.php' == $hook ) {
			
			/* CPT Styles & Scripts */
			// Media Scripts
			wp_enqueue_media( array(
	            'post' => $post_id,
	        ) );

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-ui', MODULA_URL . '/assets/css/jquery-ui.min.css' );
			wp_enqueue_style( 'modula-icons', MODULA_URL . '/assets/css/materialdesignicons.css' );
			wp_enqueue_style( 'modula-cpt-style', MODULA_URL . '/assets/css/modula-cpt.css' );
			
			wp_enqueue_script( 'modula-cpt-script', MODULA_URL . '/assets/js/modula-cpt-scripts.js', array( 'jquery', 'jquery-ui-slider', 'wp-color-picker', 'jquery-ui-sortable' ), '2.0.0', true );
			wp_enqueue_script( 'modula-image-edit-script', MODULA_URL . '/assets/js/modula-edit.js', array( 'jquery' ), '2.0.0', true );

		}

	}

}
