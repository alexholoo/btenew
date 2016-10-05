<?php

namespace Supplier\Synnex;

use Toolkit\Utils;

class FreightQuoteResult
{
    protected $shipMethods = [];

    public function add($shipMethod)
    {
        // $shipMethod['Code']
        // $shipMethod['Description']
        // $shipMethod['ServiceLevel']
        // $shipMethod['Freight']

        $this->shipMethods[] = $shipMethod;
    }

    public function getShipMethods()
    {
        return $this->shipMethods;
    }

    public function toHtml($name)
    {
        // remove 'Pick-Up'
        $shipMethods = array_filter($this->shipMethods,
            function ($val) {
                return strpos($val['Description'], 'Pick-Up') === false;
            }
        );

        // sort by cost
        $lowPriceFirst = function($a, $b) {
            if ($a['Freight'] == $b['Freight']) {
                return 0;
            }
            return ($a['Freight'] < $b['Freight']) ? -1 : 1; // ASC
        };

        usort($shipMethods, $lowPriceFirst);

        // generate html
        $lines = [];

        $lines[] = "<select name=\"$name\">";

        foreach ($shipMethods as $shipMethod) {
            $code    = $shipMethod['Code'];
            $desc    = $shipMethod['Description'];
            $level   = $shipMethod['ServiceLevel'];
            $freight = $shipMethod['Freight'];

            $lines[] = "<option value=\"$code\">$desc - ({$level}d) - $freight</option>";
        }

        $lines[] = '<option value="WHS">Auto</option>';
        $lines[] = '</select>';

        $html = implode("\n", $lines);

        return $html;
    }
}
