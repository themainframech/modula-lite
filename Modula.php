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
define( 'MODULA_VERSION', '2.0.0' );
define( 'MODULA_PATH', plugin_dir_path( __FILE__ ) );
define( 'MODULA_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-modula.php';

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

	$plugin = new Modula();

}

modula_run();