<?php

namespace Supplier\XmlApi\Synnex\PriceAvailability;

use Supplier\XmlApi\Synnex\Warehouse;

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
            $item = [];
            $item['sku'] = strval($x->synnexSKU);
            $item['status'] = strval($x->status);
            $item['price'] = strval($x->price);
            $item['totalQty'] = strval($x->totalQuantity);

            #item['mfgPN'] = $x->mfgPN;
            #item['mfgCode'] = $x->mfgCode;
            #$x->description;
            #$x->GlobalProductStatusCode;

            $item['warehouses'] = [];

            $warehouses = $x->AvailabilityByWarehouse;
            foreach($warehouses as $warehouse) {
                $info = $warehouse->warehouseInfo;
                if ($warehouse->qty > 0) {
                    $item['warehouses'][Warehouse::getName($info->number)] = strval($warehouse->qty);
                }

                #$info->zipcode;
                #$info->city;
                #$info->addr;
            }

            $this->items[] = $item;
        }

        return $this->items;
    }
}
