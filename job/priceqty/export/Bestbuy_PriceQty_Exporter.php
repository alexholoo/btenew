<?php

class Bestbuy_PriceQty_Exporter extends PriceQty_Exporter
{
    public function export()
    {
        $filename = Filenames::get('bestbuy.priceqty');
        $fp = fopen($filename, 'w');

        $columns = $this->getColumns();
        $offers = $this->getOffers();

        fputcsv($fp, $columns, ';');

        foreach ($offers as $offer) {
            $fields = array_combine($columns, array_fill(0, count($columns), '1'));

            $msku = $this->getMasterSku($offer['sku']);

            $price = $msku['best_cost'] ? $msku['best_cost']*2 : $offer['price'];
            $qty = min(round($msku['overall_qty']/5), 10);

           #echo $offer['sku'], "\t",
           #     $msku['best_cost'],   "($price)", "\t",
           #     $msku['overall_qty'], "($qty)", EOL;

            $fields['sku']                   = $offer['sku'];
            $fields['product-id']            = $offer['product_id'];
            $fields['product-id-type']       = 'SKU';
            $fields['description']           = '';
            $fields['internal-description']  = '';
            $fields['price']                 = $price;
            $fields['price-additional-info'] = '';
            $fields['quantity']              = $qty;
            $fields['min-quantity-alert']    = '';
            $fields['state']                 = '11';
            $fields['available-start-date']  = '';
            $fields['available-end-date']    = '';
            $fields['logistic-class']        = '';
            $fields['discount-start-date']   = '';
            $fields['discount-end-date']     = '';
            $fields['discount-price']        = '';
            $fields['update-delete']         = 'update';
            $fields['pim']                   = '';

            fputcsv($fp, $fields, ';');
        }

        fclose($fp);
    }

    protected function getOffers()
    {
        $sql = 'SELECT SKU              sku,
                       Product_ID       product_id,
                       Price            price,
                       Qty              qty,
                       Logistic_Class   logistic_class,
                       Activated        active
                  FROM bestbuy_ca_listing';

        return $this->db->fetchAll($sql);
    }

    protected function getColumns()
    {
        return [
            'sku',
            'product-id',
            'product-id-type',
            'description',
            'internal-description',
            'price',
            'price-additional-info',
            'quantity',
            'min-quantity-alert',
            'state',
            'available-start-date',
            'available-end-date',
            'logistic-class',
            'discount-start-date',
            'discount-end-date',
            'discount-price',
            'update-delete',
            'ehf-amount-ab',
            'ehf-amount-bc',
            'ehf-amount-mb',
            'ehf-amount-nb',
            'ehf-amount-nl',
            'ehf-amount-ns',
            'ehf-amount-nt',
            'ehf-amount-nu',
            'ehf-amount-on',
            'ehf-amount-pe',
            'ehf-amount-qc',
            'ehf-amount-sk',
            'ehf-amount-yt',
            'pim'
        ];
    }
}