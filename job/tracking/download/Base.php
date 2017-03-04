<?php

abstract class TrackingDownloader extends Job
{
    abstract public function download();
}
