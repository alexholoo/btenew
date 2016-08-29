<?php

namespace Supplier\XmlApi\PriceAvailability\Techdata;

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

        $this->items = array();

        if ($xml->Detail->LineInfo->ErrorInfo) {
            $this->status = 'ERROR'; // ?
            $this->errorMessage = strval($xml->Detail->LineInfo->ErrorInfo->ErrorDesc);
            return $this->items;
        }

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
        foreach ($xml->Detail->LineInfo as $x) {
            if ($x->ErrorInfo) {
                //continue;
            }

            $item = [];
            $item['sku']   = 'TD-' . strval($x->RefID1);
            $item['price'] = strval($x->UnitPrice1); // $x->UnitPrice2;
            $item['avail'] = [];

            #$x->ProductWeight
            #$x->ItemStatus

            foreach ($x->WhseInfo as $branch) {
                if ($branch->Qty > 0) {
                    #$branch->WhseCode
                    $item['avail'][] = [
                        'branch' => strval($branch->IDCode),
                        'qty'    => strval($branch->Qty),
                    ];
                }
            }

            $this->items[] = $item;
        }

        $this->status = 'OK';

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
