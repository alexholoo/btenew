<?php

$file = 'e:/BTE/amazon/reports/amazon_us_listings.txt';

$fp = fopen($file, 'r');

$title = fgetcsv($fp, 0, "\t");

while (($values = fgetcsv($fp, 0, "\t"))) {
    $fields = array_combine($title, $values);

    $asin   = $fields['asin1'];
    $sku    = $fields['seller-sku'];
    $fname  = "item-desc/html/$asin.html";

    if (!file_exists($fname)) {
        echo "$asin, $sku", PHP_EOL;

        $url = "https://www.amazon.com/dp/$asin";
       #exec("psexec -d wget $url -O $fname");
        exec("wget $url -O $fname");

        break;
    }
}

fclose($fp);
