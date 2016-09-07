<?php

namespace Supplier;

use Phalcon\Di;

class PriceAvailabilityLog
{
    // TODO: all of these methods should be moved into PriceAvailService
    public static function save($url, $request, $response)
    {
        $db = Di::getDefault()->get('db');

        $this->db->insertAsDict('xmlapi_pna_log',
            [
                'sku' => $request->getPartnum(),
                'url' => $url,
                'request' => $request->toXml(),
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
            if (time() - $time < 3600) {
                return $row['response'];
            }
        }

        return false;
    }

    public static function invalidate($sku)
    {
        $db = Di::getDefault()->get('db');

        $sql = "UPDATE xmlapi_pna_log SET valid=0 WHERE sku=?";

        return $db->execute($sql, array($sku));
    }
}
