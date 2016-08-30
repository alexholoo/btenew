<?php

namespace Supplier\XmlApi\PriceAvailability\Synnex;

use Supplier\XmlApi\PurchaseOrder\Synnex\Warehouse;

class Response
{
    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $items;

    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc;
        $this->parseXml();
    }

    public function getXmlDoc()
    {
        return $this->xmldoc;
    }

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        #$xml->customerNo
        #$xml->userName

        foreach ($xml->PriceAvailabilityList as $x) {
            /**
             * $item = [
             *     'sku'   => '...',
             *     'price' => '...',
             *     'avail' => [
             *         [ 'branch' => 'BRANCH-1', 'qty' => 1 ],
             *         [ 'branch' => 'BRANCH-1', 'qty' => 2 ],
             *         [ 'branch' => 'BRANCH-1', 'qty' => 3 ],
             *     ]
             * ];
             */
            $item = [];
            $item['sku'] = 'SYN-' . strval($x->synnexSKU);
            $item['status'] = strval($x->status);
            $item['price'] = strval($x->price);
            $item['totalQty'] = strval($x->totalQuantity);
            $item['avail'] = [];

            #item['mfgPN'] = $x->mfgPN;
            #item['mfgCode'] = $x->mfgCode;
            #$x->description;
            #$x->GlobalProductStatusCode;

            $warehouses = $x->AvailabilityByWarehouse;
            foreach($warehouses as $warehouse) {
                $info = $warehouse->warehouseInfo;
                if ($warehouse->qty > 0) {
                    $item['avail'][] = [
                        'branch' => strval($info->city), // Warehouse::getName($info->number),
                        'qty'    => strval($warehouse->qty),
                    ];
                }

                #$info->zipcode;
                #$info->city;
                #$info->addr;
            }

            $this->items[] = $item;
        }

        return $this->items;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items[0];
    }
}
