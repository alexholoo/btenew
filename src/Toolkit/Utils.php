<?php

namespace Toolkit;

class Utils
{
    // remove the '$' in front of price
    public static function tidyPrice($price)
    {
        $price = trim($price);
        $price = str_replace('$', '', $price);
        $price = str_replace(',', '', $price);
        return $price;
    }

    public static function safePrice($price)
    {
        $price = trim($price);
        $price = preg_replace('/([^0-9\.])/i', '', $price);
        $price = preg_replace('/(^[0]+)/i',  '', $price);
        return $price; // return string, (float)$price return float
    }

    public static function log($message, $type = '')
    {
        $filename = APP_DIR . '/logs/debug.log';

        if ($type) {
            $type = "[$type] ";
        }

        $text = date('Y-m-d h:i:s ') . $type . $message . PHP_EOL;

        error_log($text, 3, $filename);
    }

    public static function formatXml($xml)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        $xml = $dom->saveXML();
        return $xml;
    }

    /**
     * http://stackoverflow.com/questions/4708248/formatting-phone-numbers-in-php
     */
    public static function formatPhoneNumber($number, $sep = '-')
    {
        $num = str_replace(['-', '.', ' ', '(', ')'], '', $number);

        // +11234567890
        if (preg_match('/^\+\d(\d{3})(\d{3})(\d{4})$/', $num, $matches)) {
            $result = $matches[1]. $sep .$matches[2]. $sep .$matches[3];
            return $result;
        }

        // 1234567890
        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $num, $matches)) {
            $result = $matches[1]. $sep .$matches[2]. $sep .$matches[3];
            return $result;
        }

        return $number;
    }

    public static function formatCanadaZipCode($zipcode, $sep = ' ')
    {
        $code = str_replace(' ', '', strtoupper($zipcode));

        if (preg_match('/[A-Z]\d[A-Z]\d[A-Z]\d/', $code)) {
            return substr($code, 0, 3). $sep .substr($code, 3);
        }

        return $zipcode;
    }

    public static function formatCanadaPostalCode($code)
    {
        return self::formatCanadaPostalCode($code);
    }
}
