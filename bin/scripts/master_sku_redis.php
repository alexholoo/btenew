<?php

function getRedis()
{
    static $redis = null;

    if (!$redis) {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
    }

    return $redis;
}

function loadMasterSkuList()
{
    $redis = getRedis();

    $redis->flushdb();

    $skulist = fopen('w:/data/master_sku_list.csv', 'r');

    fgetcsv($skulist); // skip first line

    while (($fields = fgetcsv($skulist)) !== false) {
        $json = json_encode($fields);
        $redis->set($fields[0], $json);
        for ($i = 0; $i < 8; $i++) {
            $pn = $fields[ $i * 3 + 2 ];
            if ($pn != '') {
                $redis->set($pn, $json);
            }
        }
    }

    fclose($skulist);
}

loadMasterSkuList();
