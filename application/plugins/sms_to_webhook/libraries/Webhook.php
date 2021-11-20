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

        $response = curl_exec($conn);
        curl_close($conn);

        return response;
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