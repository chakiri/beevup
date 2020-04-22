<?php

namespace App\Service;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;


class BarCode
{
    public function generate(string $id)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($id);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(10);
        return $barcode->generate();


    }
}
