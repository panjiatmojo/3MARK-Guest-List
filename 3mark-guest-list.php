<?php

/*

Plugin Name: 3MARK Guest List

Plugin URI: http://theatmojo.com

Description: Know Your Visitor, and Block Spammer without Burden Your Page Loading

Version: 1.0

Author: Panji Tri Atmojo

*/


/**	declare all table name	**/
global $wpdb;
define('EMGL_TABLE_VISITOR_LOG', $wpdb->prefix . 'emgl_visitor_log');
define('EMGL_TABLE_COUNTRY_CODE', $wpdb->prefix . 'emgl_country_code');
define('EMGL_TABLE_VISITOR_LOG_SUMMARY', $wpdb->prefix . 'emgl_visitor_log_summary');
define('EMGL_TABLE_SPAMMER_LIST', $wpdb->prefix . 'emgl_spammer_list');
define('EMGL_TABLE_SPAMMER_LIST_TEMP', $wpdb->prefix . 'emgl_spammer_list_temp');
define('EMGL_TABLE_SWITCH_TEMP', $wpdb->prefix . 'emgl_table_switch_temp');
define('EMGL_TABLE_BLOCK_VISITOR_LOG', $wpdb->prefix . 'emgl_block_visitor_log');
define('EMGL_HOME_DIR', __DIR__);

require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once('function.php');

/**
 *
 *	Function declaration
 *
 **/

function emgl_guest_list_install()
{
	require(EMGL_HOME_DIR.'/config/config.php');

	/**	set default options value	**/
	foreach($default_value as $key => $content)
	{
		update_option($key, $content);
	}
	
    global $wpdb;

		/**	create spammer_list table if not available yet	**/
    if ($wpdb->get_var('SHOW TABLES LIKE "' . EMGL_TABLE_SPAMMER_LIST . '"') != EMGL_TABLE_SPAMMER_LIST) {
        $sql = sprintf(file_get_contents(__DIR__."/template/sql/spammer-list.sql"),EMGL_TABLE_SPAMMER_LIST); 
        $wpdb->query($sql);
    }
    
	/**	create table visitor log if not available yet	**/
    if ($wpdb->get_var('SHOW TABLES LIKE "' . EMGL_TABLE_VISITOR_LOG . '"') != EMGL_TABLE_VISITOR_LOG) {
		$sql = sprintf(file_get_contents(__DIR__."/template/sql/visitor-log.sql"),EMGL_TABLE_VISITOR_LOG);
        $wpdb->query($sql);
    }
	
		/**	create table block visitor log if not available yet	**/
    if ($wpdb->get_var('SHOW TABLES LIKE "' . EMGL_TABLE_BLOCK_VISITOR_LOG . '"') != EMGL_TABLE_BLOCK_VISITOR_LOG) {
		$sql = sprintf(file_get_contents(__DIR__."/template/sql/visitor-log.sql"),EMGL_TABLE_BLOCK_VISITOR_LOG);
        $wpdb->query($sql);        
    }
	
}

function emgl_guest_list_uninstall()
{
	if(get_option('emgl_uninstall_delete_option') == true)
	{
		/**	delete all option	**/
		global $default_value;
		
		foreach($default_value as $key => $content)
		{
			delete_option($key);
		}
	}

	if(get_option('emgl_uninstall_drop_table') == true)
	{
		global $wpdb;
		
		/**	drop the table during uninstallation	**/
		$sql = "DROP TABLE `" . EMGL_TABLE_VISITOR_LOG . "`;";
		$wpdb->query($sql);
		
		/**	drop the table during uninstallation	**/
		$sql = "DROP TABLE `" . EMGL_TABLE_SPAMMER_LIST . "`;";
		$wpdb->query($sql);
	
		/**	drop the table during uninstallation	**/
		$sql = "DROP TABLE `" . EMGL_TABLE_BLOCK_VISITOR_LOG . "`;";
		$wpdb->query($sql);	
	}
}

/**	register installation on activation hook	**/
register_activation_hook(__FILE__, 'emgl_guest_list_install');
/**	register uninstallation on deactivation hook	**/
register_deactivation_hook(__FILE__, 'emgl_guest_list_uninstall');

/**
 *
 *	Widget Class declaration
 *
 **/

class emgl_guest_list extends WP_Widget
{
    
    function __construct()
    {
        $params = array(
            'description' => '3MARK Guest List Plugin', //plugin description
            'name' => '3MARK Guest List' //title of plugin
        );
        parent::__construct('emgl_guest_list', '', $params);
    }
    
  
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    
    public function widget($args, $instance)
    {
        
        extract($args, EXTR_SKIP);
        $ipaddress = isset($instance['ip_display']) ? $instance['ip_display'] : false; // display ip address
        $stime     = isset($instance['server_time']) ? $instance['server_time'] : false; // display server time
        $fontcolor = $instance['font_color'];
        
        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
        if (!empty($title))
            echo $before_title . $title . $after_title;
        
        
        /**
         *
         *	extract the visitor data
         *
         **/
        $start_time = microtime(true);
		
        
        echo $before_widget;
        
		/**	get visitor data from table	**/
        $result = emgl_get_visitor_data();
		        
        include('template/widget.php');
        
        echo $after_widget;
    }
}

/**	register the function into widget	**/
add_action('widgets_init', 'register_emgl_guest_list');

function register_emgl_guest_list()
{
    register_widget('emgl_guest_list', 'emgl_guest_list_style');
}

/**
 *
 *	Administrator View
 *
 **/

/**	create top level menu	**/

add_action('admin_menu', 'emgl_menu');

function emgl_menu()
{
	/**	register top level menu here	**/    
    add_menu_page('Guest List', 'Guest List', "manage_options", 'emgl_top_menu', 'emgl_top_menu');


	/**	register the dashboard page	by override the top level slug	**/
	add_submenu_page('emgl_top_menu', 'Guest List Dashboard', "Dashboard","manage_options", 'emgl_top_menu', 'emgl_top_menu');
	
	/**	register the option page	**/
	add_submenu_page('emgl_top_menu', 'Guest List Options', "Options","manage_options", 'emgl_option_menu', 'emgl_option_menu');
}

function emgl_top_menu()
{
	/**	call dashboard menu as default page to show	**/
	emgl_dashboard_menu();	
}

function emgl_option_menu()
{
	/**	show the option page	**/
    if (!current_user_can('administrator')) {
        
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    //	load the template of administrator menu form
	include('template/option.php');

	/**	load the required styling **/
	require('template/option-style.php');
		
}

function emgl_dashboard_menu()
{
	/**	show the dashboard content	**/
    
    if (!current_user_can('administrator')) {
        
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    //	load the template of administrator menu form
	include('template/dashboard.php');
}

?>
