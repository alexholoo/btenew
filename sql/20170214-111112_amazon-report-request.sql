USE bte;

CREATE TABLE IF NOT EXISTS `amazon_report_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_type` varchar(80) NOT NULL,
  `file_ca` varchar(80) NOT NULL,
  `file_us` varchar(80) NOT NULL,
  `start_date` varchar(20) NOT NULL,
  `ttl` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `report_type` (`report_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `amazon_report_request` (`id`, `report_type`, `file_ca`, `file_us`, `start_date`, `ttl`) VALUES
	(1, '_GET_MERCHANT_LISTINGS_DATA_LITER_', 'MerchantListingsDataLiter_ca.txt', 'MerchantListingsDataLiter_us.txt', '', '1 days'),
	(2, '_GET_AFN_INVENTORY_DATA_', 'FBACAD.txt', 'FBAUSA.txt', '', '1 days'),
	(3, '_GET_ORDERS_DATA_', 'OrdersData_ca.txt', 'OrdersData_us.txt', '', '1 days'),
	(4, '_GET_MERCHANT_LISTINGS_DATA_', 'amazon_ca_listings.txt', 'amazon_us_listings.txt', '', '1 days'),
	(5, '_GET_FLAT_FILE_ORDERS_DATA_', 'amazon_ca_order_report.txt', 'amazon_us_order_report.txt', '-7 days', '10 minutes'),
	(6, '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_', 'amazon_ca_FBA.txt', 'amazon_us_FBA.txt', '-7 days', '10 minutes'),
	(7, '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_', 'amazon_ca_unshipped.txt', 'amazon_us_unshipped.txt', '-7 days', '10 minutes'),
	(8, '_GET_REFERRAL_FEE_PREVIEW_REPORT_', 'amazon_ca_referral_report.txt', 'amazon_us_referral_report.txt', '-7 days', '1 days'),
	(9, '_GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_', 'amazon-ca-payment.txt', 'amazon-us-payment.txt', '-7 days', '1 days'),
	(10, '_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_', 'amazon-ca-fba-fee.txt', 'amazon-us-fba-fee.txt', '-30 days', '1 days');
