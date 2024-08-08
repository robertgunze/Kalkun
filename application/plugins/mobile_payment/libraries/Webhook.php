<?php 
/**
 * simple http client helper
 */
class Webhook {

    function __construct() {

	}

    public function get ($url, $params = []) {
        $conn = curl_init();
        if ( is_array($params) && !empty($params) )
		{
			$url = $url . '?' . $this->_params_to_query($params);
		}

        curl_setopt($conn, CURLOPT_URL, $url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($conn);
        curl_close($conn);

        return $response;
    }

	public function post ($url, $data = array()) {
		$conn = curl_init();

        $payload = json_encode($data);

		curl_setopt($conn, CURLOPT_URL, $url);
		curl_setopt($conn, CURLOPT_POST, 1);
		curl_setopt($conn, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($conn, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($conn);
        curl_close($conn);

        return $response;
	}

    private function _params_to_query($params)
	{
		if ( !is_array($params) || empty($params) )
		{
			return '';
		}

		$query = '';

		foreach	( $params as $key => $value )
		{
			$query .= $key . '=' . $value . '&';
		}

		return substr($query, 0, strlen($query) - 1);;
	}

	/**
	 * A method to post to fulfilling service 
	 * @param string $country
	 * @param string $processor
	 * @param object $merchant
	 * @param string $service
	 * @param object $payment
     * @return  object
	 */
	public function prepare_post_data ($country, $phoneCode, $processor, $merchant, $service, $payment) {

		return array(
			"requestId" => "{$payment->id}",
			"service" => $service,
			"payer" => array(
				"name" => $payment->name,
				"account" => $payment->phonenumber,
				"accountType" => "WALLET",///Can be BANK,WALLET,etc
				"accountProvider" => $processor, //Name of the Payer Account Provider
				"country" => $merchant->country_iso_code
			),
			"customer" => array(
				"number" => $payment->phonenumber,
				"name" => $payment->name,
				"mobile" => "{$phoneCode}{$payment->phonenumber}",
				"institution" => $merchant->name,
				"category" => "" ///Can be Domestic,industrial,etc bases on type of utility. etc
			),
			"payment" => array(
				"timeStamp" => $payment->time, ////DateTime when the Payment actually happened
				"referenceNumber" => $payment->phonenumber,///Specific Reference Number used during Payment processing. eng. controlNumber,MEter Number or Specific Payment Reference incase it is different from control number modality
				"receiptNumber" => $payment->receipt,
				"deviceNumber" => "",//Can be memter number, can be any IOT device Number
				"amount" => $payment->amount,
				"currency" => $merchant->currency,
				"credits" => NULL, ///When this is not set the application eill look for tarrifs from the tarrifs management service or tarrifs details provided
				"description" => "{$service} usage payment for {$payment->phonenumber}"
			),
			"tariff" => array(),
			"callBackUrl" => "",
			"metaData" => array()

		);
	}
}

?>