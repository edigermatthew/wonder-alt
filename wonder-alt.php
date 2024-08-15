<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://wonderjarcreative.com
 * 
 * @since 0.1.0 
 * 
 * @package Wonder_Alt
 *
 * @wordpress-plugin
 * Plugin Name:       Wonder Alt
 * Plugin URI:        https://wonderjarcreative.com
 * Description:       Automatically add alt text to images.
 * Version:           1.4.0
 * Author:            Matthew Ediger
 * Author URI:        https://wonderjarcreative.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wonder-alt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WONDER_ALT_VERSION', '1.4.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wonder-alt-activator.php
 */
function activate_wonder_alt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wonder-alt-activator.php';
	Wonder_Alt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wonder-alt-deactivator.php
 */
function deactivate_wonder_alt() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wonder-alt-deactivator.php';
	Wonder_Alt_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wonder_alt' );
register_deactivation_hook( __FILE__, 'deactivate_wonder_alt' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wonder-alt.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_wonder_alt() {
	$plugin = new Wonder_Alt();
	$plugin->run();
}
run_wonder_alt();