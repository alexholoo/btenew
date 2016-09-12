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
         *     <PARTNUM>52T</PARTNUM>
         *     <UNITPRICE>6.63</UNITPRICE>
         *     <BRANCHQTY>
         *         <BRANCH>Toronto</BRANCH>
         *         <QTY>84</QTY>
         *         <INSTOCKDATE />
         *     </BRANCHQTY>
         *     <TOTALQTY>84</TOTALQTY>
         *  </ITEM>
         *  <ITEM>
         *     <PARTNUM>123ABC</PARTNUM>
         *     <MESSAGE>Invalid Item Number</MESSAGE>
         *  </ITEM>
         *  <STATUS>success</STATUS>
         *  </XMLRESPONSE>
         */
        $xml = simplexml_load_string($this->xmldoc);

        $result = new PriceAvailabilityResult();

        $result->status = strval($xml->STATUS);
        if ($result->status == 'success') {
            $result->status = Response::STATUS_OK;
        }

        foreach ($xml->ITEM as $xitem) {
            $item = new PriceAvailabilityItem();

            if (empty($xitem->UNITPRICE)) {
                $xitem->UNITPRICE = 99999;
            }

            $item->sku   = 'DH-'. strval($xitem->PARTNUM);
            $item->price = strval($xitem->UNITPRICE);

            if ($xitem->BRANCHQTY) {
                $item->avail = [
                    [
                        'branch' => strval($xitem->BRANCHQTY->BRANCH),
                        'code'   => strval($xitem->BRANCHQTY->BRANCH),
                        'qty'    => strval($xitem->BRANCHQTY->QTY),
                    ]
                ];
                $item->instockDate = strval($xitem->BRANCHQTY->INSTOCKDATE);
            };

            if ($xitem->TOTALQTY) {
                $item->totalQty = strval($xitem->TOTALQTY);
            }

            if ($xitem->MESSAGE) {
                $item->status = strval($xitem->MESSAGE);
            }

            $result->add($item);
        }

        return $result;
    }
}
