<?php

abstract class OrderMerger extends Job
{
    protected $masterFile;

    public function setMasterFile($masterFile)
    {
        $this->masterFile = $masterFile;
    }

    abstract public function merge();
}
