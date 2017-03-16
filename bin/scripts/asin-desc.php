<?php

date_default_timezone_set("America/Toronto");

$db = new \Phalcon\Db\Adapter\Pdo\Mysql(
    array(
        "host"     => "localhost",
        "username" => "root",
        "password" => "",
        "dbname"   => "bte",
        "options"  => array(
        #   PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'UTF8\'",
        #   PDO::ATTR_CASE               => PDO::CASE_LOWER
        )
    )
);

$file = 'w:/data/master_sku_list.csv';

$fp = fopen($file, 'r');
$title = fgetcsv($fp);
$title[1] = 'PN';

$cnt = 0;
while (($values = fgetcsv($fp))) {
    $fields = array_combine($title, $values);

    $sku   = str_replace('overstock ', '', $fields['PN']);
    $asin  = getASIN($db, $sku);
    $fname = "item-desc/html/$asin.html";

    if (!$asin) {
        continue;
    }

    if (!file_exists($fname)) {
        echo "$asin, $sku", PHP_EOL;

        $url = "https://www.amazon.com/dp/$asin";
       #exec("psexec -d wget $url -O $fname");
        exec("wget $url -O $fname");

        // not in office hours
        if (date('H') < 11 || date('H') >= 18) {
            if (++$cnt < 2) {
                sleep(30);
            } else {
                break;
            }
        } else {
            break;
        }
    }
}

fclose($fp);

function getASIN($db, $sku)
{
    $sql = "SELECT asin FROM amazon_us_listings WHERE sku='$sku'";
    $result = $db->fetchOne($sql);
    if (!$result) {
        return false;
    }
    return $result['asin'];
}
