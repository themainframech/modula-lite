<?php
/**
 * Plugin Name: Gallery - A WordPress Modula Gallery
 * Plugin URI: https://wp-modula.com/
 * Description: Modula is one of the best & most creative WordPress gallery plugins. Use it to create a great grid or
 * masonry image gallery.
 * Author: Macho Themes
 * Version: 2.0.0
 * Author URI: https://www.machothemes.com/
 */

/**
 * Define Constants
 *
 * @since    2.0.0
 */
define( 'MODULA_LITE_VERSION', '2.0.0' );
define( 'MODULA_PATH', plugin_dir_path( __FILE__ ) );
define( 'MODULA_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-modula-activator.php
 */
function modula_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-modula-activator.php';
	Modula_Activator::activate();
}

register_activation_hook( __FILE__, 'modula_activate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-modula.php';

/**
 * The plugin feedback class that is used to collect feedback about our plugin.
 */
require plugin_dir_path( __FILE__ ) . 'includes/libraries/class-modula-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function modula_run() {

	// Our core class
	$plugin = new Modula();

	// Our feedback class
	$plugin_feedback = new Modula_Feedback( __FILE__ );

}

modula_run();