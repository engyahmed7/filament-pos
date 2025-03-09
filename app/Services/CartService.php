<?php

namespace App\Services;

use TomatoPHP\FilamentEcommerce\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function addToCart(Product $product, int $quantity = 1): void
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'barcode' => $product->barcode
            ];
        }
        
        Session::put('cart', $cart);
    }
}
