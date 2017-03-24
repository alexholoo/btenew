<?php

abstract class Pricelist_Importer extends Job
{
    abstract public function import();
}
