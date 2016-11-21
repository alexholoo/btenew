<?php

use PHPUnit\Framework\TestCase;

use Supplier\Model\Order;
use Supplier\Model\OrderItem;
use Supplier\Model\OrderAddress;

class OrderTest extends TestCase
{
    public function testOrder()
    {
        $info = $this->getOrder();

        $order = new Order($info);

        $this->assertEquals($order->channel,     $info['channel']);
        $this->assertEquals($order->date,        $info['date']);
        $this->assertEquals($order->orderId,     $info['orderId']);
        $this->assertEquals($order->express,     $info['express']);
        $this->assertEquals($order->shipMethod,  $info['shipMethod']);
        $this->assertEquals($order->branch,      $info['branch']);
        $this->assertEquals($order->comment,     htmlspecialchars($info['comment']));
        $this->assertEquals($order->notifyEmail, null);

        $this->assertInstanceOf('Supplier\\Model\\OrderAddress', $order->shippingAddress);

        $this->assertTrue(is_array($order->items));
        $this->assertCount(1, $order->items);
        $this->assertInstanceOf('Supplier\\Model\\OrderItem', $order->items[0]);
    }

    public function testOrderItem()
    {
        $order = $this->getOrder();

        $item = new OrderItem($order);

        $this->assertEquals($item->orderId, $order['orderId']);
        $this->assertEquals($item->sku,     $order['sku']);
        $this->assertEquals($item->price,   $order['price']);
        $this->assertEquals($item->qty,     $order['qty']);
    }

    public function testOrderAddress()
    {
        $order = $this->getOrder();

        $address = new OrderAddress($order);

        $this->assertEquals($address->contact,    $order['buyer']);
        $this->assertEquals($address->address,    $order['address']);
        $this->assertEquals($address->city,       $order['city']);
        $this->assertEquals($address->province,   $order['province']);
        $this->assertEquals($address->state,      $order['province']);
        $this->assertEquals($address->zipcode,    $order['postalcode']);
        $this->assertEquals($address->postalcode, $order['postalcode']);
        $this->assertEquals($address->country,    $order['country']);
        $this->assertEquals($address->phone,      $order['phone']);
        $this->assertEquals($address->email,      $order['email']);
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
