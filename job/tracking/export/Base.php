<?php

abstract class TrackingExporter extends Job
{
    abstract public function export();
}
