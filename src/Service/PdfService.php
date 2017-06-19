<?php

namespace Service;

use Phalcon\Di\Injectable;

class PdfService extends Injectable
{
    public function addBarCode($filename, $output)
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdfile = $parser->parseFile($filename);

        // Retrieve all pages from the pdf file.
        $pages = $pdfile->getPages();

        $fpdf = new \FPDI();
        $fpdf->setSourceFile($filename);

        // define barcode style
        $style = array(
            'position'     => '',
            'align'        => 'R',
            'stretch'      => false,
            'fitwidth'     => false,
            'cellfitalign' => '',
            'border'       => false,
            'hpadding'     => 'auto',
            'vpadding'     => 'auto',
            'fgcolor'      => array(0,0,0),
            'bgcolor'      => false, //array(255,255,255),
            'text'         => true,
            'font'         => 'helvetica',
            'fontsize'     => 6,
            'stretchtext'  => 4
        );

        // Loop over each page to extract text.
        foreach ($pages as $index => $page) {
            $lines = explode("\n", $page->getText());
            foreach ($lines as $line) {
                $line = str_replace([' ', '­'], ['', '-'], $line);
                if (preg_match('/Order ID:/', $line)) {
                    $fpdf->AddPage();
                    $tplIdx = $fpdf->importPage($index + 1);
                    $fpdf->useTemplate($tplIdx);

                    $orderId = substr($line, 12);
                    $fpdf->write1DBarcode($orderId, 'C128', '', '', '', 12, 0.3, $style, 'N');

                    break;
                }
            }
        }

        $fpdf->Output($output, 'F');
    }
}
