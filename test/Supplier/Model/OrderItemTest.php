<?php

use PHPUnit\Framework\TestCase;

use Supplier\Model\Order;
use Supplier\Model\OrderItem;
use Supplier\Model\OrderAddress;

class OrderItemTest extends TestCase
{
    public function testOrderItem()
    {
        $order = $this->getOrder();

        $item = new OrderItem($order);

        $this->assertEquals($item->orderId, '701-3707503-5766610');
        $this->assertEquals($item->sku,     'SYN-5637038');
        $this->assertEquals($item->price,   '87.39');
        $this->assertEquals($item->qty,     '1');
    }

    public function getOrder()
    {
        return [ // this comes from ca_order_notes
            'id' => '2754',
            'channel' => 'Amazon-ACA',
            'date' => '2016-08-29',
            'orderId' => '701-3707503-5766610',
            'mgnOrderId' => '',
            'express' => '0',
            'buyer' => 'Sam Wang',
            'address' => '123 Esna Park',
            'city' => 'Toronto',
            'province' => 'ON',
           #'province' => 'Ontario',
            'postalcode' => 'M9W 5Z9',
            'country' => 'CA',
            'phone' => '800-900-1020',
            'email' => 'samwang@email.com',
            'sku' => 'SYN-5637038',
            'price' => '87.39',
            'qty' => '1',
            'shipping' => '0.00',
            'mgnInvoiceId' => 'n/a',
            // extra info from user
            'branch' => '57',
            'comment' => 'Price match D&H $335.55',
            'shipMethod' => 'UPX',
        ];
    }
}
