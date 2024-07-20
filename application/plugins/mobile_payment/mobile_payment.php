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
	//log_message('info',var_dump($config));
	$message = $sms->TextDecoded;
	$from = $sms->SenderNumber;
    $smscenter = $sms->SMSCNumber;
	
	$webhook_url = $config['webhook_url'];
	
    $CI =& get_instance();
    $CI->load->model('mobile_payment/mobile_payment_model', 'plugin_model');
    $CI->load->library('mobile_payment/webhook', 'webhook');
	$CI->load->library('mobile_payment/TransactionMapper', 'txnmapper');
    
	//process payment and forward to an end-point
	$countryISOCode = $CI->plugin_model->get_country_iso_code();
	$merchant = $CI->plugin_model->get_merchant();
	log_message('info', "libraries/parsers/{$countryISOCode}_*.php");
	log_message('info', var_dump(glob("libraries/parsers/{$countryISOCode}_*.php")));
	foreach (glob("libraries/parsers/{$countryISOCode}_*.php") as $file) {
		require_once($file);

		$class = basename($file, '.php');
		$processor = $class;
		$class = substr($class, 3, strlen($class) - 1);//remove country code prefix on filename;
		log_message('info',$class);
		if (class_exists($class) && $class::alias == $from) {
			//$transactionMapper = new TransactionMapper(new $class);
			$transactionMapper = $CI->txnmapper->set_payment_processor(new $class);
			$transactionMapper->input = $message;
			$transactionData = $transactionMapper.processTransaction();
			$transactionData['merchant_id'] = $merchant->merchant_id;
			//save transaction data
			if ($transaction_id = $CI->plugin_model->save_transaction($transactionData)) {
				$service = $merchant->service;
				$transactionData['id'] = $transaction_id;
				$payment = (object)$transactionData;
				$payload = $CI->webhook->prepare_post_data($countryISOCode, $processor, $merchant, $service, $payment);
				$response = $CI->webhook->post($webhook_url, $payload);
				log_message('info', var_dump($response));
			}
			break;
		}
	}
}

/* End of file mobile_payment.php */
/* Location: ./application/plugins/mobile_payment/mobile_payment.php */