<?php

namespace Supplier\DH;

use Supplier\Model\Response;
use Supplier\Model\PriceAvailabilityItem;
use Supplier\Model\PriceAvailabilityResult;
use Supplier\Model\PriceAvailabilityResponse as BaseResponse;

class PriceAvailabilityResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\PriceAvailabilityResult
     */
    public function parseXml()
    {
        /**
         *  <?xml version="1.0" encoding="UTF-8" ?>
         *  <XMLRESPONSE>
         *  <ITEM>
         *      <PARTNUM>01SSC8592</PARTNUM>
         *      <BRANCHQTY>
         *          <BRANCH>Mississauga</BRANCH>
         *          <QTY>0</QTY>
         *          <INSTOCKDATE></INSTOCKDATE>
         *      </BRANCHQTY>
         *      <TOTALQTY>0</TOTALQTY>
         *  </ITEM>
         *  <ITEM>...</ITEM>
         *  <STATUS>success</STATUS>
         *  </XMLRESPONSE>
         */
        $xml = simplexml_load_string($this->xmldoc);

        $this->items = array();
        $this->status = strval($xml->STATUS);

        foreach ($xml->ITEM as $item) {
            if (empty($item->BRANCHQTY->QTY))
                $item->BRANCHQTY->QTY = 0;

            if (empty($item->UNITPRICE))
                $item->UNITPRICE = 99999;

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
            $this->items[] = array(
                'sku'   => 'DH-'. strval($item->PARTNUM),
                'price' => strval($item->UNITPRICE),
                'avail' => [
                    [
                        'branch' => strval($item->BRANCHQTY->BRANCH),
                        'qty'    => strval($item->BRANCHQTY->QTY),
                    ]
                ],
                'instockDate' => strval($item->BRANCHQTY->INSTOCKDATE),
                'totalQty'    => strval($item->TOTALQTY),
            );
        }

        return $this->items;
    }
}
