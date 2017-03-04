<?php

abstract class PriceQtyExporter extends Job
{
    abstract public function export();
}
