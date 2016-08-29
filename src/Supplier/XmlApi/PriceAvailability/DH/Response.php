<?php

namespace Supplier\XmlApi\PriceAvailability\DH;

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

    /**
     * @param  string $xmldoc
     *
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
                'sku'   => strval($item->PARTNUM),
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
