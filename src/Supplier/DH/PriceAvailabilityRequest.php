<?php

namespace Supplier\DH;

use Toolkit\Utils;
use Supplier\Model\PriceAvailabilityRequest as BaseRequest;

class PriceAvailabilityRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function build()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();

        $lines[] = "<XMLFORMPOST>";
        $lines[] =   "<REQUEST>price-availability</REQUEST>";
        $lines[] =   "<LOGIN>";
        $lines[] =   "<USERID>$username</USERID>";
        $lines[] =   "<PASSWORD>$password</PASSWORD>";
        $lines[] =   "</LOGIN>";
        $lines[] =   $this->partnumList();
        $lines[] = "</XMLFORMPOST>";

        $xmldoc = Utils::formatXml(implode("\n", $lines));

        return $xmldoc;

       #return "xmlDoc=" . $xmldoc; // Post an HTML form
       #return "xmlDoc=" . rawurlencode($xmldoc); // More safer way to post an HTML form
    }

    /**
     * @return string
     */
    protected function partnumList()
    {
        $lines = array();

        foreach ($this->partnums as $partnum) {
            if (substr($partnum, 0, 3) == 'DH-') {
                $partnum = substr($partnum, 3);
            }

            $lines[] = "<PARTNUM>$partnum</PARTNUM>";
        }

        return implode("\n", $lines);
    }
}
