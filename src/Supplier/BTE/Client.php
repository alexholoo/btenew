<?php

namespace Supplier\BTE;

use Phalcon\Di;

use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;

use Supplier\Model\PriceAvailabilityResult;
use Supplier\Model\PriceAvailabilityItem;
use Supplier\Model\Response;

class Client extends BaseClient
{
    /**
     * @param  string $sku
     */
    public function getPriceAvailability($sku)
    {
        $di = Di::getDefault();

        $inventoryService = $di->get('inventoryService');

        $row = $inventoryService->get($sku);

        $price = 0;
        $qty = 0;

        if ($row) {
            $qty = $row['qty'];
            $price = $row['selling_cost'];
        }

        $item = new PriceAvailabilityItem($res);
        $item->sku = $sku;
        $item->price = $price;
        $item->avail = [
            [ 'branch' => 'Markham', 'code' => '01', 'qty' => $qty ],
        ];

        $result = new PriceAvailabilityResult();
        $result->status = Response::STATUS_OK;
        $result->add($item);

        return $result;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function purchaseOrder($order)
    {
        throw \Exception('Purchase Order not supported for BTE');
    }

    public function getOrderStatus($orderId)
    {
        throw \Exception('Order Status not supported for BTE');
    }
}
