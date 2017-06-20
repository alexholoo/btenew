<?php

namespace App\Controllers;

class ToolsController extends ControllerBase
{
    public function barcodeAction()
    {
        $filenames = [
            'E:/Orders',
            'E:/Orders-AmazonCA',
        ];

        if ($this->request->isPost()) {
            foreach ($filenames as $filename) {
                if (file_exists("$filename.pdf")) {
                    $this->pdfService->addBarCode("$filename.pdf", "$filename-Barcode.pdf");
                }
            }
        }

        $fileinfo = [];
        foreach ($filenames as $filename) {
            $info = [];
            $info['filename'] = "$filename.pdf";
            $info['output']   = "$filename-Barcode.pdf";
            $info['exists']   = file_exists($info['filename']);
            $info['created']  = file_exists($info['output']);
            $fileinfo[] = $info;
        }

        $this->view->fileinfo = $fileinfo;
    }
}
