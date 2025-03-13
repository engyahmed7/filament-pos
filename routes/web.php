<?php

use App\Http\Controllers\BarcodeController;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use TomatoPHP\FilamentEcommerce\Models\Cart;
use TomatoPHP\FilamentEcommerce\Models\Product;


// dd(Filament::getResources());
Route::get('/', function () {
    return view('welcome');
});


Route::get('products/{model}/print', function (Product $model) {
    return view('vendor.filament-ecommerce.products.print', compact('model'));
})->name('product.print');

Route::get('products/print-all', function () {
    $products = Product::all();
    return view('vendor.filament-ecommerce.products.print-all', compact('products'));
})->name('products.print-all');

Route::get('/pos/scan/{barcode}', function (Request $request, $barcode) {
    Log::info($barcode);
    $product = Product::where('barcode', $barcode)->first();
    Log::info($product);
    if (!$product || $product === null) {
        Log::info('Product not found');
        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }

    $cartItem = Cart::where('session_id', session()->get('sessionID'))
        ->where('product_id', $product->id)
        ->first();

    if ($cartItem) {
        $cartItem->qty += 1;
        $cartItem->total = ($cartItem->price + $cartItem->vat) * $cartItem->qty - $cartItem->discount;
        $cartItem->save();
    } else {
        Cart::create([
            'session_id' => session()->get('sessionID'),
            'product_id' => $product->id,
            'item' => $product->name,
            'qty' => 1,
            'price' => $product->price,
            'discount' => $product->discount,
            'vat' => $product->vat,
            'total' => ($product->price + $product->vat) - $product->discount
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => "Added {$product->name} to cart"
    ]);
});
