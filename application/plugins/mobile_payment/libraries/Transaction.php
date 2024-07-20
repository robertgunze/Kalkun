<?php

/**
 * Description of Transaction
 *
 * @author robertgunze
 */
class Transaction {

    const MONEY_IN = 1;
	const MONEY_OUT = 2;
	const MONEY_NEUTRAL = 3;

    const PAYMENT_RECEIVED = 21;
    const PAYMENT_SENT = 22;
    const DEPOSIT = 23;
    const WITHDRAW = 24;
    const WITHDRAW_ATM = 25;
    const PAYBILL_PAID = 26;
    const BUY_GOODS = 27;
    const AIRTIME_YOU = 28;
    const AIRTIME_OTHER = 29;
    const UNKNOWN = 30;   
}

?>
