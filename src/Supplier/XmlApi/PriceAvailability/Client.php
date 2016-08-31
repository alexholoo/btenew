<?php

namespace Supplier\XmlApi\PriceAvailability;

use Supplier\XmlApi\Client as XmlApiClient;

abstract class Client extends XmlApiClient
{
    protected function saveLog($url, $request, $response)
    {
        $this->db->insertAsDict('xmlapi_pna_log',
            [
                'sku' => $request->getPartnum(),
                'url' => $url,
                'request' => $request->toXml(),
                'response' => $response->getXmlDoc(),
                'status' => $response->getStatus(),
            ]
        );
    }

    protected function queryLog($sku)
    {
        $sql = "SELECT * FROM xmlapi_pna_log WHERE sku=? ORDER BY id DESC LIMIT 1";

        $result = $this->db->query($sql, array($sku));

        if ($result) {
            $row = $result->fetch(\Phalcon\Db::FETCH_ASSOC);

            $time = strtotime($row['time']);
            if (time() - $time < 3600) {
                return $row['response'];
            }
        }

        return false;
    }
}
