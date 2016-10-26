<?php

namespace Supplier\DH;

use Toolkit\Utils;
use Toolkit\FtpClient;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\OrderStatusQueryLog;
use Supplier\DropshipTrackingLog;
use Supplier\ConfigKey;
use Supplier\Model\Response;
use Supplier\Model\OrderStatusResult;

class Client extends BaseClient
{
    const PA_PROD_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';
    const PO_PROD_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';
    const OS_PROD_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';

    /**
     * @param  string $sku
     */
    public function getPriceAvailability($sku)
    {
        if ($res = PriceAvailabilityLog::query($sku)) {
            $response = new PriceAvailabilityResponse($res);
            $this->request = null;
            $this->response = $response;
            return $response->parseXml();
        }

        $url = self::PA_PROD_URL;

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::DH]);
        $request->addPartnum($sku);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new PriceAvailabilityResponse($res);
        $result = $response->parseXml();

        PriceAvailabilityLog::save($url, $request, $response);

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function purchaseOrder($order)
    {
        $url = self::PO_PROD_URL;

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::DH]);
        $request->addOrder($order);

        $xml = $request->toXml();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new PurchaseOrderResponse($res);
        $result = $response->parseXml();

        $this->di->get('logger')->debug(Utils::formatXml($response->getXmlDoc()));

        PurchaseOrderLog::saveXml($url, $request, $response);

        if ($result->status == Response::STATUS_OK) {
            PurchaseOrderLog::save($order['sku'], $order['orderId'], $result->orderNo);
            PriceAvailabilityLog::invalidate($order['sku']);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    public function getOrderStatus($orderId)
    {
        $url = self::OS_PROD_URL;

        $request = new OrderStatusRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::DH]);
        $request->setOrder($orderId);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new OrderStatusResponse($res);

        $result = $response->parseXml();

        OrderStatusQueryLog::save($orderId, $url, $xml, $res);

        if ($result->trackingNumber) {
            PurchaseOrderLog::update($orderId);
            DropshipTrackingLog::save($result);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    public function getTracking()
    {
        $config = require APP_DIR . '/config/ftp.php';
        $account = $config['ftp']['DH'];

        $ftp = new FtpClient([
            'hostname' => $account['Host'],
            'username' => $account['User'],
            'password' => $account['Pass'],
        ]);

        $columns = [
            // 0,    1,             2,           3,              4,          5,
            [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
            [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
            [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        ];

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        if ($ftp->connect()) {
            $localFile = 'E:/BTE/DH-TRACKING';
            $ftp->download('TRACKING', $localFile);

            // import to dropship_tracking
            $fp = fopen($localFile, 'r');
            while ($fields = fgetcsv($fp, 0, '|')) {
                if ($fields[0] == 'H1') {
                    $fields = array_map('trim', $fields);

                    $result = new OrderStatusResult();
                    $result->orderNo = $fields[1];

                    $fields = fgetcsv($fp, 0, '|');

                    if ($fields[0] == 'D1') {
                        $fields = array_map('trim', $fields);

                        $result->trackingNumber = $fields[1];
                        $result->carrier = $fields[2]. ' ' .$fields[3];
                        $result->service = $fields[4];
                        $result->shipDate = $fmtdate($fields[5]);
                    }

                    if ($result->trackingNumber) {
                        echo $result->orderNo, ' ', $result->trackingNumber, EOL;
                        #var_export($result);
                        DropshipTrackingLog::save($result);
                    }
                }
            }

            fclose($fp);
        }
    }
}
