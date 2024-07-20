
CREATE TABLE IF NOT EXISTS `plugin_mobile_payment_merchant` (
  `merchant_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `country_iso_code` varchar(5) NOT NULL,
  `service` varchar(60) NOT NULL,
  `currency` varchar(5) NOT NULL,
  PRIMARY KEY (`merchant_id`)
) ENGINE = MYISAM;

CREATE TABLE IF NOT EXISTS `plugin_mobile_payment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) NOT NULL,
  `super_type` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `receipt` varchar(10) NOT NULL,
  `time` datetime NOT NULL,
  `phonenumber` varchar(45)  NOT NULL,
  `name` varchar(255) NOT NULL,
  `account` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `post_balance` bigint(20) NOT NULL,
  `note` varchar(255) NOT NULL,
  `transaction_cost` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt` (`receipt`),
  KEY `type_index` (`type`),
  KEY `name_index` (`name`),
  KEY `phone_index` (`phonenumber`),
  KEY `time_index` (`time`),
  KEY `super_index` (`super_type`),
  KEY `fk_plugin_mobile_payment_transaction_merchants` (`merchant_id`)
) ENGINE = MYISAM;