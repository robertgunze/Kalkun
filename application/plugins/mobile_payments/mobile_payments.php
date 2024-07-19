<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Plugin Name: Mobile Payments
* Plugin URI: https://github.com/robertgunze/Kalkun.git
* Version: 0.1
* Description: Mobile payments processor based on SMS from MNOs
* Author: Robert Gunze
* Author URI: https://nexis.co.tz
*/

function mobile_payments_initialize()
{
	$CI =& get_instance();

	$CI->load->add_package_path(APPPATH.'plugins/mobile_payments', FALSE);
	$CI->load->config('mobile_payments', TRUE);

	return $CI->config->config['mobile_payments'];
}

// Add hook for incoming message
add_action("message.incoming.before", "mobile_payments", 15);

/**
* Function called when plugin first activated
* Utility function must be prefixed with the plugin name
* followed by an underscore.
* 
* Format: pluginname_activate
* 
*/
function mobile_payments_activate()
{
    return true;
}

/**
* Function called when plugin deactivated
* Utility function must be prefixed with the plugin name
* followed by an underscore.
* 
* Format: pluginname_deactivate
* 
*/
function mobile_payments_deactivate()
{
    return true;
}

/**
* Function called when plugin first installed into the database
* Utility function must be prefixed with the plugin name
* followed by an underscore.
* 
* Format: pluginname_install
* 
*/
function mobile_payments_install()
{
	$CI =& get_instance();
	//$CI->load->helper('kalkun');
    //TODO: create a history table
	// check if table already exist
	// if (!$CI->db->table_exists('plugin_mobile_payments'))
	// {
	// 	$db_driver = $CI->db->platform();
	// 	$db_prop = get_database_property($db_driver);
	// 	execute_sql(APPPATH."plugins/mobile_payments/media/".$db_prop['file']."_mobile_payments.sql");
	// }	
    return true;
}

function mobile_payments($sms)
{
	$config = mobile_payments_initialize();
	print_r($config);
	$message = $sms->TextDecoded;
	$number = $sms->SenderNumber;
    $smscenter = $sms->SMSCNumber;
	
	$webhook_url = $config['webhook_url'];
	
    $CI =& get_instance();
    //$CI->load->model('mobile_payments/mobile_payments_model', 'plugin_model');
    $CI->load->library('mobile_payments/webhook', 'webhook');
    
	//process payment and forward to an end-point
}

/* End of file mobile_payments.php */
/* Location: ./application/plugins/mobile_payments/mobile_payments.php */
