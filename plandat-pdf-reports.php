<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.onlineoptimisation.com.au
 * @since             1.0.0
 * @package           Plandat
 *
 * @wordpress-plugin
 * Plugin Name:       PlanDAT PDF Reports
 * Plugin URI:        https://www.plandat.com.au
 * Description:       PlanDat is an online system to create PDF reports feeding data live from the NSW planning portal. This report is sold online to customers. There are 3 main interfaces to the system for the users; Staff, Suppliers (eg Shed companies), Customers (eg home owners).
 * Version:           1.0.0
 * Author:            Online Optimisation
 * Author URI:        https://www.onlineoptimisation.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plandat-pdf-reports
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLANDAT_PDF_VERSION', '1.0.0' );
define( 'PLANDAT_PDF_URL', plugin_dir_url( __FILE__ ) );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plandat-pdf-reports.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plandat_pdf() {

	$plugin = new Plandat_PDF_Reports();
	$plugin->run();

}
run_plandat_pdf();
