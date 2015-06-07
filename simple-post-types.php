<?php
/**
 *
 * @link              http://github.com/pehaa/pht-simple-post-types
 * @since             1.0.0
 * @package           PHT_Simple_Post_Types
 *
 * @wordpress-plugin
 * Plugin Name:       PHT Simple Post Types
 * Plugin URI:        http://github.com/pehaa/pht-simple-post-types
 * Description:       Adds custom post types and taxonomies
 * Version:           1.0.0
 * Author:            PeHaa THEMES
 * Author URI:        http://wptemplates.pehaa.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pht-simple-post-types
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/pehaa/pht-simple-post-types
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'PHT_SPT_ACTIVATION_DATA_FIELD' ) ) define( 'PHT_SPT_ACTIVATION_DATA_FIELD', 'phtspt-activation' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-post-types-activator.php
 */
function activate_simple_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-post-types-activator.php';
	PHT_Simple_Post_Types_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-post-types-deactivator.php
 */
function deactivate_simple_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-post-types-deactivator.php';
	PHT_Simple_Post_Types_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simple_post_types' );
register_deactivation_hook( __FILE__, 'deactivate_simple_post_types' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simple-post-types.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_post_types() {

	$plugin = new PHT_Simple_Post_Types();
	$plugin->run();

}
run_simple_post_types();