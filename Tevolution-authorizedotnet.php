<?php 
/*
 *Plugin Name: Templatic Authorize.net
 *Plugin URI: http://templatic.com/docs/authorizenet/
 *Description: Seamlessly integrate Authorize.net payment gateway with the Tevolution from templatic.com.
 *Version: 1.0.4
 *Author: Templatic
 *Author URI: http://templatic.com
*/

//define the plugin directory path
define('TEMPL_FILE_PATH_AUTHORIZE', plugin_dir_path(__FILE__));
define( 'AUTHORIZE_VERSION', '1.0.4' );
define('AUTHORIZE_PLUGINS_SLUG','Tevolution-authorizedotnet/Tevolution-authorizedotnet.php');
if(!defined('DOMAIN'))
	define('DOMAIN', 'templatic');

if(!defined('AUTHORIZE_PLUGIN_BASENAME')) {
	define( 'AUTHORIZE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
add_action('activated_plugin','save_error_authorize');
function save_error_authorize(){
    update_option('plugin_error',  ob_get_contents());
}
//echo get_option('plugin_error');
require_once ABSPATH.'wp-admin/includes/upgrade.php';

//call function file
require_once TEMPL_FILE_PATH_AUTHORIZE.'authorize_functions.php'; 

// Activation and Deactivation hooks for plugin
register_activation_hook(__FILE__,'templ_add_method_install_authorizenet');
register_deactivation_hook(__FILE__,'templ_add_method_deactivate_authorizenet');

if(strstr($_SERVER['REQUEST_URI'],'plugins.php')){
	require_once('wp-updates-plugin.php');
	new WPAuthorizeUpdates( 'http://templatic.com/updates/api/index.php', plugin_basename(__FILE__) );
}

/*
 * Function Name: admanager_update_login
 * Return: update admanager_update_login plugin version after templatic member login
 */
add_action('wp_ajax_authorize','authorize_update_login');
function authorize_update_login()
{
	check_ajax_referer( 'authorize', '_ajax_nonce' );
	$plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );	
	require_once( $plugin_dir .  '/templatic_login.php' );	
	exit;
}
?>