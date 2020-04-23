<?php

namespace App\Service;
use App\Entity\User;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;


class BarCode
{
    public function generate(string $id)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($this->create(User::DEFAULT_BAR_CODE, $id));
        $barcode->setType(BarcodeGenerator::Ean13);


        $barcode->setScale(2);
        $barcode->setThickness(25);
        $barcode->setFontSize(10);
        return $barcode->generate();


    }

    public function create(string $defaultNumber, string $id)
    {
       $missingNumbers = "";
       $missingNumbersCount = 12 - strlen($defaultNumber) - strlen($id);
       for ($i = 1; $i <=  $missingNumbersCount; $i++) {
            $missingNumbers =  $missingNumbers . "0";
        }
       return $defaultNumber . $missingNumbers . $id;
    }
}
