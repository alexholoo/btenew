<?php

namespace Shipment;

use Marketplace\Newegg\StdOrderListFile;

class NeweggShipmentFile extends StdOrderListFile
{
    // Shipment file has the same format as OrderList file

    public function getHeader()
    {
        $headers = parent::getHeader();

        $headers['US'][] = 'ShipDate';

        return $headers[$this->site];
    }
}
