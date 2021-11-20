<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Plugin Name: SMS to Webhook
* Plugin URI: https://github.com/robertgunze/Kalkun.git
* Version: 0.1
* Description: Sending SMS to a webhook
* Author: Robert Gunze
* Author URI: https://nexis.co.tz
*/

error_reporting(E_ALL);

function sms_to_webhook_initialize()
{
	$CI =& get_instance();

	$CI->load->add_package_path(APPPATH.'plugins/sms_to_webhook', FALSE);
	$CI->load->config('sms_to_webhook', TRUE);

	return $CI->config->config['sms_to_webhook'];
}

// Add hook for incoming message
add_action("message.incoming.before", "sms_to_webhook", 15);

/**
* Function called when plugin first activated
* Utility function must be prefixed with the plugin name
* followed by an underscore.
* 
* Format: pluginname_activate
* 
*/
function sms_to_webhook_activate()
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
function sms_to_webhook_deactivate()
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
function sms_to_webhook_install()
{
	$CI =& get_instance();
	//$CI->load->helper('kalkun');
    //TODO: create a history table
	// check if table already exist
	// if (!$CI->db->table_exists('plugin_sms_to_webhook'))
	// {
	// 	$db_driver = $CI->db->platform();
	// 	$db_prop = get_database_property($db_driver);
	// 	execute_sql(APPPATH."plugins/sms_to_webhook/media/".$db_prop['file']."_sms_to_webhook.sql");
	// }	
    return true;
}

function sms_to_webhook($sms)
{
	$config = sms_to_webhook_initialize();
	print_r($config);
	$message = $sms->TextDecoded;
	$number = $sms->SenderNumber;
    $smscenter = $sms->SMSCNumber;
	
	$webhook_url = $config['webhook_url'];

	print_r($webhook_url);
	
    $CI =& get_instance();
    //$CI->load->model('sms_to_webhook/sms_to_webhook_model', 'plugin_model');
    $CI->load->library('sms_to_webhook/webhook', 'webhook');
    
    //$response = $CI->webhook->get($webhook_url, array('phone' => $number, 'text' => $message, 'smscenter'=> $smscenter));
    //autoreply($sms, $response);
	$response = file_get_contents($webhook_url + '?' + "phone=$number&text=" + urlencode($message) + "&smscenter=$smscenter");
	file_get_contents("http://localhost/index.php/plugin/rest_api/send_sms?phoneNumber=$number&message=" + urlencode($response));
	
}

function autoreply ($sms, $reply) {

    $CI =& get_instance();
    $CI->load->model('Message_model');
	$data['coding'] = 'default';
	$data['class'] = '1';
	$data['dest'] = $sms->SenderNumber;
	$data['date'] = date('Y-m-d H:i:s');
	$data['message'] = $reply;
	$data['delivery_report'] = 'default';
    $data['uid'] = '1';
	$CI->Message_model->send_messages($data);
}

/* End of file sms_to_webhook.php */
/* Location: ./application/plugins/sms_to_webhook/sms_to_webhook.php */
