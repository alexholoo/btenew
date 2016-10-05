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
        $lines = [];

        $lines[] = "<select name=\"$name\">";
        $lines[] = '<option value="WHS">Auto</option>';

        foreach ($this->shipMethods as $shipMethod) {
            $code    = $shipMethod['Code'];
            $desc    = $shipMethod['Description'];
            $freight = $shipMethod['Freight'];

            $lines[] = "<option value=\"$code\">$desc - $freight</option>";
        }

        $lines[] = '</select>';

        $html = implode("\n", $lines);

        return $html;
    }
}
