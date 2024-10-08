<?php

/**
Plugin Name: Sidebar Manager Light
Plugin URI: http://otwthemes.com/?utm_source=wp.org&utm_medium=admin&utm_content=site&utm_campaign=sml
Description:  Create custom sidebars (widget areas) and replace any existing sidebar so you can display relevant content on different pages.
Author: OTWthemes
Version: 1.18
Author URI: https://codecanyon.net/user/otwthemes/portfolio?ref=OTWthemes
*/
$wp_sml_int_items = array(
	'page'              => array( array(), esc_html__( 'Pages' ), esc_html__( 'All pages' ) ),
	'userroles'         => array( array(), esc_html__( 'User roles/Logged in as', 'otw_sml' ), esc_html__( 'All roles', 'otw_sml' ) )
);

/**
 * Loaded plugin 
 */
function otw_sml_plugin_loaded(){
	
	global $otw_plugin_options, $otw_sml_plugin_url, $wp_sml_int_items, $otw_sml_factory_component, $otw_sml_factory_object, $otw_sml_plugin_id;
	
	//load text domain
	load_plugin_textdomain('otw_sml',false,dirname(plugin_basename(__FILE__)) . '/languages/');
	
	$otw_sml_plugin_id = '093851eae230bf1ed146ac2856531b60';
	
	$otw_sml_factory_component = false;
	$otw_sml_factory_object = false;
	
	//load core component functions
	@include_once( 'include/otw_components/otw_functions/otw_functions.php' );
	
	if( !function_exists( 'otw_register_component' ) ){
		wp_die( 'Please include otw components' );
	}
	
	//register factory component
	otw_register_component( 'otw_factory', dirname( __FILE__ ).'/include/otw_components/otw_factory/', $otw_sml_plugin_url.'include/otw_components/otw_factory/' );
	
}

global $otw_plugin_options;

$otw_plugin_options = get_option( 'otw_plugin_options' );

$otw_sml_plugin_url = plugin_dir_url( __FILE__);

require_once( plugin_dir_path( __FILE__ ).'/include/otw_functions.php' );

/** calls list of available sidebars
  *
  */
function otw_sml_sidebars_list(){
	if( otw_get('action',false) && otw_get('action','') == 'edit' ){
		require_once( 'include/otw_manage_sidebar.php' );
	}else{
		require_once( 'include/otw_list_sidebars.php' );
	}
}

/** calls page where to create new sidebars
  *
  */
function otw_sml_sidebars_manage(){;
	require_once( 'include/otw_manage_sidebar.php' );
}
/** plugin options
  *
  */
function otw_sml_sidebars_options(){
	require_once( 'include/otw_sidebar_options.php' );
}
/** delete sidebar
  *
  */
function otw_sml_sidebars_action(){
	require_once( 'include/otw_sidebar_action.php' );
}

function otw_sml_items_by_type(){
	require_once( 'include/otw_sbm_items_by_type.php' );
	die;
}
/**
 * factory messages
 */
function otw_sml_factory_message( $params ){
	
	global $otw_sml_plugin_id;
	
	if( isset( $params['plugin'] ) && $otw_sml_plugin_id == $params['plugin'] ){
		
		//filter out some messages if need it
	}
	if( isset( $params['message'] ) )
	{
		return $params['message'];
	}
	return $params;
}

/** admin menu actions
  * add the top level menu and register the submenus.
  */ 
function otw_sml_admin_actions(){
	
	global $otw_sml_plugin_url;
	
	add_menu_page('Sidebar Manager', 'Sidebar Manager', 'manage_options', 'otw-sml', 'otw_sml_sidebars_list', $otw_sml_plugin_url . 'images/otw-sbm-icon.png' );
	add_submenu_page( 'otw-sml', 'Sidebars', 'Sidebars', 'manage_options', 'otw-sml', 'otw_sml_sidebars_list' );
	add_submenu_page( 'otw-sml', 'Add Sidebar', 'Add Sidebar', 'manage_options', 'otw-sml-add', 'otw_sml_sidebars_manage' );
	add_submenu_page( 'otw-sml', esc_html__( 'Plugin Options', 'otw_sml'), esc_html__('Plugin Options', 'otw_sml'), 'manage_options', 'otw-sml-options', 'otw_sml_sidebars_options' );
	add_submenu_page( __FILE__, 'Manage widget', 'Manage widget', 'manage_options', 'otw-sml-action', 'otw_sml_sidebars_action' );
}


/** include needed javascript scripts based on current page
  *  @param string
  */
function enqueue_sml_scripts( $requested_page ){
	global $otw_sml_plugin_url;
	switch( $requested_page ){
	
		case 'toplevel_page_otw-sml':
				if( otw_get('action',false) && otw_get('action','') == 'edit' ){
					wp_enqueue_script("otw_sml_manage_sidebar", $otw_sml_plugin_url. 'js/otw_manage_sidebar.js'  , array( 'jquery' ), '1.1' );
				}
			break;
		case 'sidebar-manager_page_otw-sml-add':
				wp_enqueue_script("otw_sml_manage_sidebar", $otw_sml_plugin_url. 'js/otw_manage_sidebar.js'  , array( 'jquery' ), '1.1' );
			break;
	}
}

/**
 * include needed styles
 */
function enqueue_sml_styles( $requested_page ){
	global $otw_sml_plugin_url;
	wp_enqueue_style( 'otw_sml_sidebar', $otw_sml_plugin_url .'css/otw_sbm_admin.css', array( 'thickbox' ), '1.1' );
}
/**
 * Loaded plugin
 */
add_action( 'plugins_loaded', 'otw_sml_plugin_loaded' );
/**
 * register admin menu 
 */
add_action('admin_menu', 'otw_sml_admin_actions');
add_action('admin_notices', 'otw_sml_admin_notice');
add_filter('sidebars_widgets', 'otw_sidebars_widgets');
add_filter('otwfcr_notice', 'otw_sml_factory_message' );
/**
 * include plugin js and css.
 */
add_action('admin_enqueue_scripts', 'enqueue_sml_scripts');
add_action('admin_print_styles', 'enqueue_sml_styles' );

//register some admin actions
if( is_admin() ){
	add_action( 'wp_ajax_otw_sml_items_by_type', 'otw_sml_items_by_type' );
}
/** 
 *call init plugin function
 */
add_action('init', 'otw_sml_plugin_init', 101 );
?>
