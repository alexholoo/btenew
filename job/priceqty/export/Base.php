<?php

abstract class PriceQty_Exporter extends Job
{
    abstract public function export();
}
