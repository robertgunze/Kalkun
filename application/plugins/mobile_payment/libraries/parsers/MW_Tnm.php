<?php

/**
 * Description of TNM
 *
 * @author robert
 */

require_once(__DIR__.'/../utilities/Utility.php');
require_once(__DIR__.'/../Transaction.php');
require_once(__DIR__.'/../PaymentStrategy.php');
require_once(__DIR__.'/../Mapper.php');

class Tnm extends PaymentStrategy{

    const alias = 'TNM';
    const countryCode = '+265';

	public function dateInput($time) {
		$dt = \DateTime::createFromFormat("j/n/y h:i A", $time);
		return $dt->getTimestamp();
    }
    
    //put your code here
    public function parse(Mapper $mapper){
        //implement code to parse TNM MONEY sms from merchant's phone
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
        
		if (strpos($input, "Cash In") >= 0) {
			$result["super_type"] = Transaction::MONEY_IN;
			$result["type"] = Transaction::PAYMENT_RECEIVED;

			$regex = '/Cash In from (\d+)-([A-Z ]+) on (\d{2}\/\d{2}\/\d{4}) (\d{2}:\d{2}:\d{2})\.\s*Amt:\s*([\d,]+\.?\d*)MWK\s*Fee:\s*([\d,]+\.?\d*)MWK\s*Ref:\s*([A-Z0-9]+)\s*Bal:\s*([\d,]+\.?\d*)MWK/';

			if (preg_match($regex, $input, $matches)) {
				list($full_match, $sender_number, $sender_name, $date, $time, $amount, $fee, $reference, $balance) = $matches;

				$result["receipt"] = $reference;
				$result["amount"] = Utility::numberInput($amount);
				$result["name"] = $sender_name;
				$result["phone"] = $sender_number;
				//$result["time"] = strtotime(date("Y-m-d H:i:s"));
				$result["time"] = date("Y-m-d H:i:s");
				$result["balance"] = Utility::numberInput($balance);
			}
		
		} else {
			$result["super_type"] = Transaction::MONEY_NEUTRAL;
			$result["type"] = Transaction::UNKNOWN;
		}

		return $result;
    } 
}

?>
