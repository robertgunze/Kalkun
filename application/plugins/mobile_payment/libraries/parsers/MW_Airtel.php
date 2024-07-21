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

    const alias = 'Airtel';
    const countryCode = '+265';
    
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
                            "amount" => 0,
                            "balance" => 0,
                            "note" => "",
                            "costs" => 0
                     );
        
        // REFACTOR: should be split into subclasses
		if (strpos($input, "you have received") > 0) {
			$result["super_type"] = Transaction::MONEY_IN;
			$result["type"] = Transaction::PAYMENT_RECEIVED;

			$regex = '/Trans\.ID\s*:\s*([A-Z0-9\.]+)\.\s*Dear customer, you have received MK\s*([\d\.]+)\s*from\s*([\d]+),\s*([A-Z ]+)\s*.\s*Your balance is MK\s*([\d\.]+)\./';
			if (preg_match($regex, $input, $matches)) {
				list($full_match, $transaction_id, $amount_received, $sender_number, $sender_name, $new_balance) = $matches;
				$result["receipt"] = $transaction_id;
				//$result["amount"] = Utility::numberInput($amount_received);
				$result["amount"] = floatval(str_replace(',',$amount_received));
				$result["name"] = $sender_name;
				$result["phone"] = $sender_number;
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
