<?php

class TrackingJob extends Job
{
    protected $masterShipment;
    protected $amazonCAshipment;
    protected $amazonUSshipment;

    public function getStatus()
    {
        return 0; // 1-enabled, 2-disabled
    }

    public function setAmazonCAshipment($amazonCAshipment)
    {
        $this->amazonCAshipment = $amazonCAshipment;
    }

    public function setAmazonUSshipment($amazonUSshipment)
    {
        $this->amazonUSshipment = $amazonUSshipment;
    }

    public function setMasterShipment($masterShipment)
    {
        $this->masterShipment = $masterShipment;
    }

    public function merge() { }
    public function download() { }
}
