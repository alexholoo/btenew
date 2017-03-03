<?php

class Filenames
{
    protected static $filenames = [
        'dh.pricelist'          => 'E:/BTE/pricelist/DH-ITEMLIST',

        'synnex.pricelist.zip'  => 'E:/BTE/pricelist/syn-c1150897.zip',
        'synnex.pricelist'      => 'E:/BTE/pricelist/syn-c1150897.ap',

        'techdata.prodcod.zip'  => 'E:/BTE/pricelist/prodcode.zip',
        'techdata.prodlist.zip' => 'E:/BTE/pricelist/prodlist.zip',
        'techdata.prodmast'     => 'E:/BTE/pricelist/prodmast.txt',
        'techdata.pricelist'    => 'E:/BTE/pricelist/prodmast.txt',

        'ingram.pricelist.zip'  => 'E:/BTE/pricelist/ing-price.zip',
        'ingram.pricelist'      => 'E:/BTE/pricelist/ing-price.csv',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
