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

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $this->items = array();

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
