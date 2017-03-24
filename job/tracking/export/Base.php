<?php

abstract class Tracking_Exporter extends Job
{
    abstract public function export();
}
