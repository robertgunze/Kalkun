<?php

/**
 * Description of Airtel
 *
 * @author robert
 */

require_once(__DIR__.'/../utilities/Utility.php');
require_once(__DIR__.'/../Transaction.php');
require_once(__DIR__.'/../PaymentStrategy.php');
require_once(__DIR__.'/../Mapper.php');

class Airtel extends PaymentStrategy{

    const alias = 'AirtelMoney';
    const countryCode = '265';
    
	public function dateInput($time) {
		$dt = \DateTime::createFromFormat("j/n/y h:i A", $time);
		return $dt->getTimestamp();
    }

    //put your code here
    public function parse(Mapper $mapper){
        //implement code to parse AIRTEL MONEY sms from merchant's phone
        $input = $mapper->input;
        $result = array(
                            "super_type" => 0,
                            "type" =>0,
                            "receipt" => "",
                            "time" => 0,
                            "phone" => "",
                            "name" => "",
                            "account" => "",
                            "status" => "",
                            "amount" => 0.00,
                            "balance" => 0.00,
                            "note" => "",
                            "costs" => 0
                     );
        
        // REFACTOR: should be split into subclasses
		if (strpos($input, "You have received") > 0) {
			$result["super_type"] = Transaction::MONEY_IN;
			$result["type"] = Transaction::PAYMENT_RECEIVED;

			$regex = '/Trans ID:\s*([A-Z0-9\.]+)\s*You have received MK\s*([\d,\.]+)\s*from\s*(\d{3}\*{4}\d{2})\s*Ref #(\d+)\.\s*Your new balance is MK\s*([\d,\.]+)/';

			if (preg_match($regex, $input, $matches)) {
				list($full_match, $transaction_id, $amount_received, $masked_sender_number, $reference, $new_balance) = $matches;
				
				$result["receipt"] = $transaction_id;
				$result["amount"] = Utility::numberInput($amount_received);
				//$result["name"] = $sender_name;
				$result["phone"] = $masked_sender_number;
				$result["account"] = $reference;
				//$result["time"] = strtotime(date("Y-m-d H:i:s"));
				$result["time"] = date("Y-m-d H:i:s");
				$result["balance"] = Utility::numberInput($new_balance);

			} 

		}  else {
			$result["super_type"] = Transaction::MONEY_NEUTRAL;
			$result["type"] = Transaction::UNKNOWN;
		}

		return $result;
    } 
}

?>
