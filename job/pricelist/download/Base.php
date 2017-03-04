<?php

abstract class PricelistDownloader extends Job
{
    abstract public function download();
}
