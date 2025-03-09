<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TomatoPHP\FilamentEcommerce\Models\Product;

class BarcodeController extends Controller
{
    public function scan(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();

        if ($product) {
            // Add to cart logic here
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
}
