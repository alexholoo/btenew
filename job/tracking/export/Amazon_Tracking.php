<?php

class Amazon_Tracking extends TrackingExporter
{
    public function export()
    {
        // Nothing to do, it's done in AmazonShippingUploadJob
        //
        // It's might be better to move AmazonShippingUploadJob here
    }
}
