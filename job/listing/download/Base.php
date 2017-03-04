<?php

abstract class ListingDownloader extends Job
{
    abstract public function download();
}
