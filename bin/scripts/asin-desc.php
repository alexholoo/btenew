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
$file = 'e:/BTE/import/master_sku_list.csv';

$fp = fopen($file, 'r');
$title = fgetcsv($fp);
$title[1] = 'PN';

$failed = 0;
while (($values = fgetcsv($fp))) {
    if (count($title) != count($values)) {
        continue;
    }
    $fields = array_combine($title, $values);

    $sku   = str_replace('overstock ', '', $fields['PN']);
    $asin  = getASIN($db, $sku);
    $fname = "item-desc/html/$asin.html";

    if (!$asin) continue;
    if (file_exists($fname)) continue;

    echo "$asin, $sku", PHP_EOL;

    $url = "https://www.amazon.com/dp/$asin";

   #exec("psexec -d wget $url -O $fname");
    exec("wget $url -O $fname");

    $filesize = filesize($fname);
    if ($filesize > 0 && $filesize < 10000) {
        echo chr(7);
        if (++$failed == 3) break;
    } else $failed = 0;

    // not in office hours
    if (date('H') < 11 || date('H') >= 18) {
        sleep(30);
    } else {
        sleep(10);
       #sleep(rand(40, 60));
       #sleep(rand(90, 150));
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
