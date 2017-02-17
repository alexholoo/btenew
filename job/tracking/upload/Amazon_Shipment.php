<?php

class Amazon_Shipment extends TrackingUploader
{
    public function upload()
    {
        // Nothing to do, it's done in AmazonShippingUploadJob
        //
        // It's might be better to move AmazonShippingUploadJob here
    }
}
