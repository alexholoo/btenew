<?php

namespace Toolkit;

class Utils
{
    public static function unzip($zipfile)
    {
         $zip = new ZipArchive;
         $res = $zip->open($zipfile);
         if ($res === TRUE) {
             $zip->extractTo(dirname($zipfile));
             $zip->close();
             return true;
         }
         return false;
    }

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

    public static function renderView($__file, $__data)
    {
        ob_start();
        extract($__data);
        include("views/$__file.tpl");
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public static function render($__file, $__data, $__layout = '')
    {
        $content = renderView($__file, $__data);
        if (empty($__layout))
            $__layout = 'layout';
    //  extract($__data);
        include("views/$__layout.tpl");
    }

    public static function template($__file, $__data, $__layout = '')
    {
        ob_start();
        extract($__data);
        include("views/$__file.tpl");
        $content = ob_get_contents();
        ob_end_clean();

        if (empty($__layout)) {
            $__layout = 'layout';
        }
        include("views/$__layout.tpl");
    }

    public static function formatXml($xml)
    {
        $dom = new \DOMDocument('1.0');
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
       #$xml = $dom->saveXML($dom->documentElement);
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
