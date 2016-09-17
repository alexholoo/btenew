<?php

namespace Supplier\Synnex;

use Supplier\FreightQuoteResult;
use Supplier\Model\Response as BaseResponse;

class FreightQuoteResponse extends BaseResponse
{
    /**
     * @return Supplier\Model\FreightQuoteResult
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $result = new FreightQuoteResult();

        return $result;
    }
}
