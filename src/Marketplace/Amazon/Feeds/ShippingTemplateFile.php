<?php

namespace Marketplace\Amazon\Feeds;

class ShippingTemplateFile extends FlatFile
{
    protected $columns = ['sku', 'merchant_shipping_group_name'];
}
