<?php

namespace Toolkit;

class Utils
{
    // TODO: move files-related methods to Files class
    // Files::join/Files:deleteOld/Files::archive

    public static function joinFiles(array $files, $target)
    {
        $dest = fopen($target, "w+");

        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $src = fopen($file, "r");
            while (!feof($src)) {
                fwrite($dest, fgets($src));
            }
            fclose($src);
            fwrite($dest, "\n"); // usually last line doesn't have a newline
        }

        fclose($dest);
    }

    public static function deleteOldFiles($dir, $interval)
    {
        if (!$interval) {
            $interval = strtotime('-10 days');
        }

        // cycle through all files in the directory
        foreach (glob($dir."*") as $file) {
            // if file is too old then delete it ***/
            if (filemtime($file) <= $interval) {
                //unlink($file);
                echo $file, "\n";
            }
        }
    }

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
     * archiveFiles('../app/logs/*.xml');
     */
    public static function archiveFiles($pattern)
    {
        $files = glob($pattern);

        foreach ($files as $file) {
            $date = date('Y-m-d', filemtime($file));
            $dir = dirname($file).'/archive/'.$date;

            if (!file_exists($dir)) {
                @mkdir($dir, 0777, true);
            }

            rename($file, $dir.'/'.basename($file));
        }
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
