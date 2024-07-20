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
}

?>