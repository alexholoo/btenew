<?php

namespace Marketplace\Amazon;

use Toolkit\CsvFileWriter;

class OrderReportFile
{
    protected $channel;

    protected $csvWriter;

    protected $orderFileCA = 'E:/BTE/orders/amazon/amazon_ca_order_report.csv';
    protected $orderFileUS = 'E:/BTE/orders/amazon/amazon_us_order_report.csv';

    protected $csvTitleCA = array(
        'order-id',
        'order-item-id',
        'purchase-date',
        'payments-date',
        'buyer-email',
        'buyer-name',
        'buyer-phone-number',
        'sku',
        'product-name',
        'quantity-purchased',
        'currency',
        'item-price',
        'item-tax',
        'shipping-price',
        'shipping-tax',
        'ship-service-level',
        'recipient-name',
        'ship-address-1',
        'ship-address-2',
        'ship-address-3',
        'ship-city',
        'ship-state',
        'ship-postal-code',
        'ship-country',
        'ship-phone-number',
        'item-promotion-discount',
        'item-promotion-id',
        'ship-promotion-discount',
        'ship-promotion-id',
        'delivery-start-date',
        'delivery-end-date',
        'delivery-time-zone',
        'delivery-Instructions',
        'sales-channel',
    );

    protected $csvTitleUS = array(
        'order-id',
        'order-item-id',
        'purchase-date',
        'payments-date',
        'buyer-email',
        'buyer-name',
        'buyer-phone-number',
        'sku',
        'product-name',
        'quantity-purchased',
        'currency',
        'item-price',
        'item-tax',
        'shipping-price',
        'shipping-tax',
        'ship-service-level',
        'recipient-name',
        'ship-address-1',
        'ship-address-2',
        'ship-address-3',
        'ship-city',
        'ship-state',
        'ship-postal-code',
        'ship-country',
        'ship-phone-number',
        'delivery-start-date',
        'delivery-end-date',
        'delivery-time-zone',
        'delivery-Instructions',
        'sales-channel',
        'order-channel',
        'order-channel-instance',
        'external-order-id',
        'purchase-order-number',
    );

    public function __construct($site)
    {
        if ($site == 'CA') {
            $this->channel = 'Amazon-ACA';
            $filename = $this->orderFileCA;
            $csvTitle = $this->csvTitleCA;
        }

        if ($site == 'US') {
            $this->channel = 'Amazon-US';
            $filename = $this->orderFileUS;
            $csvTitle = $this->csvTitleUS;
        }

        $this->csvWriter = new CsvFileWriter($filename);
        $this->csvWriter->setHeadline($csvTitle);
    }

    public function write($data)
    {
        return $this->csvWriter->write($data);
    }
}
