<?php

namespace Supplier\Model;

class PriceAvailabilityItem
{
    /**
     * @var string
     */
    public $sku;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $price;

    /**
     * @var array
     *
     * avail => [
     *     [ 'branch' => 'BRANCH-1', 'qty' => 1 ],
     *     [ 'branch' => 'BRANCH-1', 'qty' => 2 ],
     *     [ 'branch' => 'BRANCH-1', 'qty' => 3 ],
     * ]
     */
    public $avail = [];

    /**
     * @return integer
     */
    public function getTotalQty()
    {
        $totalQty = 0;

        foreach ($this->avail as $avail) {
            $totalQty += $avail['qty'];
        }

        return $totalQty;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $avail = array_filter($this->avail, function($val) { return $val['qty']; });

        return [
            'sku'   => $this->sku,
            'price' => $this->price,
            'avail' => $avial,
        ];
    }
}
