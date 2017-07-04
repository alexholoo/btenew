<?php

namespace App\Controllers;

use Toolkit\File;

class ToolsController extends ControllerBase
{
    public function barcodeAction()
    {
        $this->view->pageTitle = 'Add Barcode';

        if ($this->request->isPost() && $this->request->hasFiles()) {
            $uploadDir = 'E:/BTE/uploads/';

            if (!is_dir($uploadDir.'/archive')) {
                mkdir($uploadDir.'/archive', 0777, true);
            }

            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getName() == '') {
                    continue;
                }

                $filename = $uploadDir . $file->getName();

                if (file_exists($filename)) {
                    File::archive($filename);
                }
                $file->moveTo($filename);

                $newfile = File::suffix($filename, 'Barcode');
                if (file_exists($filename)) {
                    File::archive($newfile);
                }

                $ftype = $this->request->getPost('ftype');
                if ($ftype == 'amazonOrderFile') {
                    $this->pdfService->addBarCode($filename, $newfile);
                    $this->startDownload($newfile);
                }
            }
        }
    }

    public function labelAction()
    {
        $this->view->pageTitle = 'Label with Barcode';

        if ($this->request->isPost()) {
            $sku  = $this->request->getPost('sku');
            $info = $this->skuService->getMasterSku($sku);

            if ($info) {
                $this->view->data = [
                    'sku'       => $sku,
                    'name'      => $info['name'],
                    'upc'       => $info['UPC'],
                    'condition' => 'New',
                ];
            }
        }
    }
}
