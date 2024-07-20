<?php

/**
 * Mobile_payment_model Class
 *
 */
class Mobile_payment_model extends CI_Model {

	const STATUS_COMPLETED = 1;
	const STATUS_DECLINED = 2;
	const STATUS_CANCELLED = 3;
	const STATUS_ATTEMPTED = 4;

	function __construct()
	{
		parent::__construct();
	}

    /**
     * Retrieve country ISO code e.g TZ, KE, NG etc...
     */
    function get_country_iso_code() {
        $this->db->from('plugin_mobile_payment_merchant');
        $result = $this->db->get()->row();
        return $result->country_iso_code;
    }
	
    function save_transaction($data = array())
    {
        $this->db->set($param);
        $this->db->insert('plugin_mobile_payment_transaction')
    }

	function check_transaction($receipt)
	{
		$exist = FALSE;
		$this->db->from('plugin_mobile_payment_transaction');
		$this->db->where('receipt', $receipt);
		
		if ($this->db->count_all_results() == 1)
		{
			$exist = TRUE;
		}
		return $exist;
	}
	
}

/* End of file Mobile_payment_model.php */
/* Location: ./application/plugins/mobile_payment/models/Mobile_payment_model.php */