<?php

/**
 * This class is used to read Amaozn Listing Report.
 */
class StdListingFile
{
    protected $site;
    protected $handle;
    protected $filename;
    protected $delimiter = "\t";

    public function __construct($filename, $site)
    {
        $this->filename = $filename;
        $this->site = $site;
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function read()
    {
        if (!$this->handle) {
            if (!file_exists($this->filename)) {
                return false;
            }
            $this->handle = fopen($this->filename, 'r');
            fgetcsv($this->handle, 0 , $this->delimiter); // skip the header
        }

        $fields = fgetcsv($this->handle, 0 , $this->delimiter);
        if ($fields) {
            return array_combine($this->getHeader(), $fields);
        }

        return $fields;
    }

    public function getHeader()
    {
        $headers = [
            'US' => [
                'item-name',
                'item-description',
                'listing-id',
                'seller-sku',
                'price',
                'quantity',
                'open-date',
                'image-url',
                'item-is-marketplace',
                'product-id-type',
                'zshop-shipping-fee',
                'item-note',
                'item-condition',
                'zshop-category1',
                'zshop-browse-path',
                'zshop-storefront-feature',
                'asin1',
                'asin2',
                'asin3',
                'will-ship-internationally',
                'expedited-shipping',
                'zshop-boldface',
                'product-id',
                'bid-for-featured-placement',
                'add-delete',
                'pending-quantity',
                'fulfillment-channel',
                'Business Price',
                'Quantity Price Type',
                'Quantity Lower Bound 1',
                'Quantity Price 1',
                'Quantity Lower Bound 2',
                'Quantity Price 2',
                'Quantity Lower Bound 3',
                'Quantity Price 3',
                'Quantity Lower Bound 4',
                'Quantity Price 4',
                'Quantity Lower Bound 5',
                'Quantity Price 5',
                'merchant-shipping-group',
            ],

            'CA' => [
                'item-name',
                'item-description',
                'listing-id',
                'seller-sku',
                'price',
                'quantity',
                'open-date',
                'image-url',
                'item-is-marketplace',
                'product-id-type',
                'zshop-shipping-fee',
                'item-note',
                'item-condition',
                'zshop-category1',
                'zshop-browse-path',
                'zshop-storefront-feature',
                'asin1',
                'asin2',
                'asin3',
                'will-ship-internationally',
                'expedited-shipping',
                'zshop-boldface',
                'product-id',
                'bid-for-featured-placement',
                'add-delete',
                'pending-quantity',
                'fulfillment-channel',
                'merchant-shipping-group',
            ]
        ];

        return $headers[$this->site];
    }
}
