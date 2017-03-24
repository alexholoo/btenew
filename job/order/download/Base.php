<?php

abstract class Order_Downloader extends Job
{
    abstract public function download();
}
