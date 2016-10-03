<?php

namespace Toolkit;

class CurrencyExchangeRate
{
    public static function get($from, $to)
    {
        $url = "http://finance.yahoo.com/d/quotes.csv?f=l1d1t1&s={$from}{$to}=X";
        $handle = fopen($url, 'r');

        $exchangeRate = 1.00; // 1.25

        if ($handle) {
            $result = fgetcsv($handle);

            if (isset($result[0])) {
                $exchangeRate = $result[0];
            }

            fclose($handle);
        }

        return $exchangeRate;
    }
}

//echo CurrencyExchangeRate::get('USD', 'CAD');
