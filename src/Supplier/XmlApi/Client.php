<?php

namespace Supplier\XmlApi;

use Phalcon\Di;

class Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param  array $config
     */
    public function __construct($config)
    {
         $this->config = $config;
         $this->di = Di::getDefault();
         $this->db = $this->di->get('db');
    }

    /**
     * @param  string $url
     * @param  string $data
     */
    public function curlPost($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param  string $url
     * @param  string $xml
     */
    public function send($url, $xml)
    {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded",
                'content' => $xml,
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, NULL, $context);

        return $result;
    }

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
}
