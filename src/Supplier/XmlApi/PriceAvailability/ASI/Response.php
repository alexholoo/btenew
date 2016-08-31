<?php

namespace Supplier\XmlApi\PriceAvailability\ASI;

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

        if ($xml->error) {
            $this->status = 'ERROR';
            $this->errorMessage = $xml->error->message;
            return;
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
        $item = array(
            'sku'    => 'AS-'. strval($xml->Inventory['SKU']),
            'price'  => strval($xml->Inventory->Price),
            'avail'  => [ ],
            'status' => strval($xml->Inventory->Status),
        );

        foreach($xml->Inventory->Qty->Branch as $branch) {
            if ($branch > 0) {
                $item['avail'][] = [
                    'branch' => strval($branch['Name']),
                    'qty'    => strval($branch),
                ];
            }
        }

        $this->status = 'OK';

        $this->items[] = $item;
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
