<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TransactionMapper
 *
 * @author robert
 */
class TransactionMapper{
    //put your code here
        const MPESA='M-PESA';
        const AIRTEL='AIRTEL';
        const TIGO='TIGO';
        const EASYPESA='EASYPESA';
        
        private $paymentStrategy;
        public $input='';
        
        function __construct(PaymentStrategy $paymentStrategy){
            $this->paymentStrategy = $paymentStrategy;
        }
    
     public function processTransaction(){
            $transData = $this->paymentStrategy->parse($this);
            if(!empty($transData))
                return $transData;
            
            return false;
            
     }
     
     
    
}

?>
