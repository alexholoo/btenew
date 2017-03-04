<?php

abstract class PricelistImporter extends Job
{
    abstract public function import();
}
