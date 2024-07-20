<?php

/**
 * Description of MpesaParser
 *
 * @author robert
 */

require_once(__DIR__.'/../utilities/Utility.php');
require_once(__DIR__.'/../Transaction.php');
require_once(__DIR__.'/../PaymentStrategy.php');
require_once(__DIR__.'/../Mapper.php');

class Mpesa extends PaymentStrategy{

	const alias = 'M-PESA';
    const countryCode = '+255';
    
    
    function __construct() {
       
    }
    
    public function dateInput($time) {
		$dt = \DateTime::createFromFormat("j/n/y h:i A", $time);
		return $dt->getTimestamp();
    }
    
    public function parse(Mapper $mapper) {
        //implement code to parse M-PESA sms from merchant's phone
        
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
		if (strpos($input, "You have received") > 0) {
			$result["super_type"] = Transaction::MONEY_IN;
			$result["type"] = Transaction::PAYMENT_RECEIVED;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+You have received Tsh([0-9\.\,]+) from[\s\n]+([A-Z ]+) +on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			//print_r($temp[1][0]);die;
                        if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = $temp[3][0];
				//$result["phone"] = $temp[4][0];
				$result["time"] = $this->dateInput($temp[4][0] . " " . $temp[5][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} elseif (preg_match("/sent to .+ for account/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::PAYBILL_PAID;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+Tsh([0-9\.\,]+) sent to[\s\n]+(.+)[\s\n]+for account (.+)[\s\n]+on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = $temp[3][0];
				$result["account"] = $temp[4][0];
				$result["time"] = $this->dateInput($temp[5][0] . " " . $temp[6][0]);
				$result["balance"] = Utility::numberInput($temp[7][0]);
			}

		} elseif (preg_match("/Tsh[0-9\.\,]+ paid to /", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::BUY_GOODS;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+Tsh([0-9\.\,]+) paid to[\s\n]+([.]+)[\s\n]+on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = $temp[3][0];
				$result["time"] = $this->dateInput($temp[4][0] . " " . $temp[5][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} elseif (preg_match("/sent to .+ on/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::PAYMENT_SENT;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+Tsh([0-9\.\,]+) sent to ([A-Z ]+) ([0-9]+) on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = $temp[3][0];
				$result["phone"] = $temp[4][0];
				$result["time"] = $this->dateInput($temp[5][0] . " " . $temp[6][0]);
				$result["balance"] = Utility::numberInput($temp[7][0]);
			}

		} elseif (preg_match("/Give Tsh[0-9\.\,]+ cash to/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_IN;
			$result["type"] = Transaction::DEPOSIT;
			
			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+Give Tsh([0-9\.\,]+) cash to (.+)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[4][0]);
				$result["name"] = $temp[5][0];
				$result["time"] = $this->dateInput($temp[2][0] . " " . $temp[3][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} elseif (preg_match("/Withdraw Tsh[0-9\.\,]+ from/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::WITHDRAW;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed\.[\s\n]+on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+Withdraw Tsh([0-9\.\,]+) from (.+)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[4][0]);
				$result["name"] = $temp[5][0];
				$result["time"] = $this->dateInput($temp[2][0] . " " . $temp[3][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} elseif (preg_match("/Tsh[0-9\.\,]+ withdrawn from/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::WITHDRAW_ATM;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) Confirmed[\s\n]+on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M).[\s\n]+Tsh([0-9\.\,]+) withdrawn from (\d+) - AGENT ATM\.[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[4][0]);
				$result["name"] = $temp[5][0];
				$result["time"] = $this->dateInput($temp[2][0] . " " . $temp[3][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} elseif (preg_match("/You bought Tsh[0-9\.\,]+ of airtime on/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::AIRTIME_YOU;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) confirmed\.[\s\n]+You bought Tsh([0-9\.\,]+) of airtime on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = "Vodacom";
				$result["time"] = $this->dateInput($temp[3][0] . " " . $temp[4][0]);
				$result["balance"] = Utility::numberInput($temp[5][0]);
			}

		} elseif (preg_match("/You bought Tsh[0-9\.\,]+ of airtime for (\d+) on/", $input) > 0) {
			$result["super_type"] = Transaction::MONEY_OUT;
			$result["type"] = Transaction::AIRTIME_OTHER;

			$temp = array();
			preg_match_all("/([A-Z0-9]+) confirmed\.[\s\n]+You bought Tsh([0-9\.\,]+) of airtime for (\d+) on (\d\d?\/\d\d?\/\d\d) at (\d\d?:\d\d [AP]M)[\s\n]+New M-PESA balance is Tsh([0-9\.\,]+)/mi", $input, $temp);
			if (isset($temp[1][0])) {
				$result["receipt"] = $temp[1][0];
				$result["amount"] = Utility::numberInput($temp[2][0]);
				$result["name"] = $temp[3][0];
				$result["time"] = $this->dateInput($temp[4][0] . " " . $temp[5][0]);
				$result["balance"] = Utility::numberInput($temp[6][0]);
			}

		} else {
			$result["super_type"] = Transaction::MONEY_NEUTRAL;
			$result["type"] = Transaction::UNKNOWN;
		}

		return $result;
        
    }
}

?>
