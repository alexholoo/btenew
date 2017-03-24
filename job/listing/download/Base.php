<?php

abstract class Listing_Downloader extends Job
{
    abstract public function download();
}
