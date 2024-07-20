<?php

/**
 * Description of PaymentStrategy
 *
 * @author robert
 */

abstract class PaymentStrategy {
    //put your code here
    abstract function parse(Mapper $transaction);
    
}

?>
