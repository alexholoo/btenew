<?php

namespace Toolkit;

class Xml
{
    public static function format($xml)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        $xml = $dom->saveXML();
        return $xml;
    }
}
