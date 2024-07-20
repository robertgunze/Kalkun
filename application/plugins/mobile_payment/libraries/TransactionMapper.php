<?php

/**
 * Description of TransactionMapper
 *
 * @author robertgunze
 */
class TransactionMapper{
        
    private $paymentStrategy;
    public $input='';
    
    function __construct(/*PaymentStrategy $paymentStrategy*/){
        //$this->paymentStrategy = $paymentStrategy;
    }

    public function set_payment_processor(PaymentStrategy $strategy) {
        $this->paymentStrategy = $strategy;
        return $this;
    }
    
    public function processTransaction(){
        $transData = $this->paymentStrategy->parse($this);
        if(!empty($transData)) {

            return $this->transform($transData);
        }
        
        return false;
            
    }

    private function transform($data = array()) {

        $transaction = array();
        $transaction['id'] = 0;
        $transaction['amount'] = $data['amount'];
        $transaction['receipt'] = $data['receipt'];
        $transaction['merchant_id'] = $data['merchant_id'];
        $transaction['name'] = $data['name'];
        $transaction['post_balance'] = $data['balance'];
        $transaction['time'] = $data['time'];
        $transaction['phonenumber'] = $data['phone'];
        $transaction['transaction_cost'] = $data['costs'];
        $transaction['super_type'] = $data['super_type'];
        $transaction['type'] = $data['type'];
        $transaction['account'] = $data['account'];
        $transaction['status'] = $data['status'];

        return $transaction;

    }
     
     
    
}

?>
