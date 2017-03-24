<?php

abstract class Tracking_Downloader extends Job
{
    abstract public function download();
}
