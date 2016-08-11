<?php

namespace Supplier\XmlApi;

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
}
