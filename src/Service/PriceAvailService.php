<?php

namespace Service;

use Supplier\Supplier;
use Phalcon\Di\Injectable;

class PriceAvailService extends Injectable
{
    // see Supplier\XmlApi\PriceAvailability\Client;

    public function getPriceAvailability($items)
    {
        $data = [];

        foreach ($items as $sku) {
            $client = Supplier::createClient($sku);
            if ($client) {
                $result = $client->getPriceAvailability($sku);
                $data[] = $result->getFirst()->toArray();
            }

            #$data[] = [
            #    'sku' => $sku,
            #    'price' => rand(30, 200),
            #    'avail' => [
            #        [ 'branch' => 'MISSISSAUGA', 'qty' => rand(10, 30) ],
            #        [ 'branch' => 'RICHMOND',    'qty' => rand(10, 50) ],
            #        [ 'branch' => 'MARKHAM',     'qty' => rand(10, 70) ],
            #    ]
            #];
        }

        return $this->sortPriceAvailability($data);
    }

    protected function sortPriceAvailability($data)
    {
        $lowPriceFirst = function($a, $b) {
            if ($a['price'] == $b['price']) {
                return 0;
            }
            return ($a['price'] < $b['price']) ? -1 : 1; // ASC
        };

        $maxQtyFirst = function($a, $b) {
            if ($a['qty'] == $b['qty']) {
                return 0;
            }
            return ($a['qty'] < $b['qty']) ? 1 : -1; // DESC
        };

        foreach ($data as &$item) {
            usort($item['avail'], $maxQtyFirst);
        }

        usort($data, $lowPriceFirst);

        return $data;
    }
}
