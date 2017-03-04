<?php

abstract class NewItemsExporter extends Job
{
    abstract public function export();
}
