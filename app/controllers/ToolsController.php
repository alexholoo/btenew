<?php

namespace App\Controllers;

class ToolsController extends ControllerBase
{
    public function barcodeAction()
    {
        $filename = 'E:/Orders.pdf';
        $output   = 'E:/Orders-Barcode.pdf';
        if (file_exists($filename)) {
            $this->pdfService->addBarCode($filename, $output);
        }
    }
}
