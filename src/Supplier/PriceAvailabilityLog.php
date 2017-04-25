<?php

namespace Supplier;

use Phalcon\Di;

class PriceAvailabilityLog
{
    protected static $ttl = 3600;

    public static function setTTL($hours)
    {
        self::$ttl = $hours * 3600;
    }

    // TODO: all of these methods should be moved into PriceAvailService
    public static function save($url, $request, $response)
    {
        $db = Di::getDefault()->get('db');

        $db->insertAsDict('xmlapi_pna_log',
            [
                'sku' => $request->getPartnum(),
                'url' => $url,
                'request' => $request->build(),
                'response' => $response->getXmlDoc(),
               #'status' => $response->getStatus(),
            ]
        );
    }

    public static function query($sku)
    {
        $db = Di::getDefault()->get('db');

        $sql = "SELECT * FROM xmlapi_pna_log WHERE sku=? AND valid=1 ORDER BY id DESC LIMIT 1";

        $result = $db->query($sql, array($sku));

        if ($result) {
            $row = $result->fetch(\Phalcon\Db::FETCH_ASSOC);

            $time = strtotime($row['time']);
            if (time() - $time < self::$ttl) {
                return $row['response'];
            }
        }

        return false;
    }

    public static function invalidate($order)
    {
        $db = Di::getDefault()->get('db');

        foreach ($order->items as $item) {
            $sku = $item->sku;
            $sql = "UPDATE xmlapi_pna_log SET valid=0 WHERE sku=?";
            $db->execute($sql, array($sku));
        }
    }
}
