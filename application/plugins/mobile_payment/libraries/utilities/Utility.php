<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utility
 *
 * @author robert
 */
class Utility {
    
	public static function numberInput($input) {
		$input = trim($input);
		$amount = 0;

		if (preg_match("/^[0-9,]+$/", $input)) {
			$amount = 100 * (int)str_replace(',', '', $input);
		} elseif (preg_match("/^[0-9,]+\.[0-9]$/", $input)) {
			$amount = 10 * (int)str_replace(array('.', ','), '', $input);
		} elseif (preg_match("/^[0-9,]*\.[0-9][0-9]$/", $input)) {
			$amount = (int)str_replace(array('.', ','), '', $input);
		} else {
			$amount = (int)$input;
		}
		return $amount;
	}

	public static function dateInput($input) {
		$timeStamp = strtotime($input);
		if ($timeStamp != FALSE) {
			return $timeStamp;
		}
		return 0;
	}
        
        public static function sendSystemEmail($from='Pesawallet',$subject,$body)
        {
                $headers="From: {$from}\r\nReply-To: noreply";
	        mail(Yii::app()->params['adminEmail'],$subject,$body,$headers);
              
        }
}

?>
