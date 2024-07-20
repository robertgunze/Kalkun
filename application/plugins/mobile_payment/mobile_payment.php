<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Plugin Name: Mobile Payments
* Plugin URI: https://github.com/robertgunze/Kalkun.git
* Version: 0.1
* Description: Mobile payments processor based on SMS from MNOs
* Author: Robert Gunze
* Author URI: https://nexis.co.tz
*/

function mobile_payment_initialize()
{
	$CI =& get_instance();

	$CI->load->add_package_path(APPPATH.'plugins/mobile_payment', FALSE);
	$CI->load->config('mobile_payment', TRUE);

	return $CI->config->config['mobile_payment'];
}

// Add hook for incoming message
add_action("message.incoming.before", "mobile_payment", 15);

/**
* Function called when plugin first activated
* Utility function must be prefixed with the plugin name
* followed by an underscore.
* 
* Format: pluginname_activate
* 
*/
function mobile_payment_activate()
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
function mobile_payment_deactivate()
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
function mobile_payment_install()
{
	$CI =& get_instance();
	$CI->load->helper('kalkun');
	//check if table already exist
	if (!$CI->db->table_exists('plugin_mobile_payment_merchants'))
	{
		$db_driver = $CI->db->platform();
		$db_prop = get_database_property($db_driver);
		execute_sql(APPPATH."plugins/mobile_payment/media/".$db_prop['file']."_mobile_payment.sql");
	}	
    return true;
}

function mobile_payment($sms)
{
	$config = mobile_payment_initialize();
	print_r($config);
	$message = $sms->TextDecoded;
	$from = $sms->SenderNumber;
    $smscenter = $sms->SMSCNumber;
	
	$webhook_url = $config['webhook_url'];
	
    $CI =& get_instance();
    $CI->load->model('mobile_payment/mobile_payment_model', 'plugin_model');
    $CI->load->library('mobile_payment/webhook', 'webhook');
    
	//process payment and forward to an end-point
	$countryISOCode = $CI->plugin_model->get_country_iso_code();

	foreach (glob("libraries/parsers/{$countryISOCode}_*.php") as $file) {
		require_once($file);

		$class = basename($file, '.php');
		$class = substr($class, 3);//remove country code prefix on filename;
		if (class_exists($class) && $class::alias == $from) {//TODO: use SIM card number country code to avoid alias collisions
			$transactionMapper = new TransactionMapper(new $class);
			$transactionMapper.input = $message;
			$transactionData = $transactionMapper.processTransaction();
			//save transaction data
			if ($CI->plugin_model->save_transaction($transactionData)) {
				//TODO: forward to webhook

			}
			break;
		}
	}
}

/* End of file mobile_payment.php */
/* Location: ./application/plugins/mobile_payment/mobile_payment.php */
