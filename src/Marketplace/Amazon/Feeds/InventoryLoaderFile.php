<?php

namespace Marketplace\Amazon\Feeds;

class InventoryLoaderFile extends FlatFile
{
    protected $columns = [
        'sku',
        'product-id',
        'product-id-type',
        'price',
        'minimum-seller-allowed-price',
        'maximum-seller-allowed-price',
        'item-condition',
        'quantity',
        'add-delete',
        'will-ship-internationally',
        'expedited-shipping',
        'item-note',
        'fulfillment-center-id',
    ];
}
