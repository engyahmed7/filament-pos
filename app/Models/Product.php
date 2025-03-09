<?php

namespace App\Models;

use Milon\Barcode\DNS1D;
use TomatoPHP\FilamentEcommerce\Models\Product;

class CustomProduct extends Product
{
    public function getBarcodeImage()
    {
        return '<img src="data:image/png;base64,' . base64_encode(DNS1D::getBarcodePNG($this->barcode, 'C128')) . '" />';
    }
}
