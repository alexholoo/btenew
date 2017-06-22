<?php

namespace App\Controllers;

use Toolkit\File;

class ToolsController extends ControllerBase
{
    public function barcodeAction()
    {
        if ($this->request->isPost() && $this->request->hasFiles()) {
            $uploadDir = 'E:/BTE/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755);
                mkdir($uploadDir.'/archive', 0755);
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

                if ($file->getKey() == 'amazonOrderFile') {
                    $this->pdfService->addBarCode($filename, $newfile);
                }
            }
        }
    }
}
