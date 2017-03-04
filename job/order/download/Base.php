<?php

abstract class OrderDownloader extends Job
{
    abstract public function download();
}
