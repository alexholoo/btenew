<?php

namespace Supplier\BTE;

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
        $accdb = $this->openAccessDB();

        $sql = "SELECT [Selling Cost], QtyOnHand FROM [bte-inventory] WHERE [Part Number]='$sku'";
        $row = $accdb->query($sql)->fetch();

        $price = 0;
        $qtyOnHand = 0;

        if ($row) {
            $qtyOnHand = $row['QtyOnHand'];
            $price = $row['Selling Cost'];
        }

        $item = new PriceAvailabilityItem($res);
        $item->sku = $sku;
        $item->price = $price;
        $item->avail = [
            [ 'branch' => 'Markham', 'code' => '01', 'qty' => $qtyOnHand ],
        ];

        $result = new PriceAvailabilityResult();
        $result->status = Response::STATUS_OK;
        $result->add($item);

        return $result;
    }

    protected function openAccessDB()
    {
        $dbname = "z:/BTE-Price-List/bte-inventory.accdb";

        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/bte-inventory.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new \PDO($dsn);

        return $db;
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
