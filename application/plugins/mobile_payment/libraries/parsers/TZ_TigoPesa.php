<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TigoParser
 *
 * @author robert
 */

require_once(__DIR__.'/../utilities/Utility.php');
require_once(__DIR__.'/../Transaction.php');
require_once(__DIR__.'/../PaymentStrategy.php');
require_once(__DIR__.'/../Mapper.php');

class TigoParser extends PaymentStrategy{

  const alias = 'TIGOPESA';
  const countryCode = '+255';

    //put your code here
  public function parse(Mapper $transaction){
      //implement code to parse TIGO PESA sms from merchant's phone
      $transData = array(
            'amount_received'=>1000000,
            'merchant_id'=>'MID-UCC',
            'transaction_id'=>'NHVER37749D7',
            'client_number'=>'CLIENT-MID-UCC-6789',
            'processor_type'=>'Tigo Pesa'
        );
        return $transData;
  }
}

?>
