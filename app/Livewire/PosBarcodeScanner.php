<?php

namespace App\Http\Livewire;

use Livewire\Component;
use TomatoPHP\FilamentEcommerce\Models\Cart;
use TomatoPHP\FilamentEcommerce\Models\Product;

class PosBarcodeScanner extends Component
{
    public $sessionID;

    public function mount()
    {
        $this->sessionID = session()->getId();
    }

    public function scanBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            $this->dispatch(
                'error',
                title: __('Product Not Found'),
                message: __('No product found with this barcode')
            );
            return;
        }

        // Check if product already in cart
        $existingCartItem = Cart::where('session_id', $this->sessionID)
            ->where('product_id', $product->id)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->update([
                'qty' => $existingCartItem->qty + 1,
                'total' => ($existingCartItem->price + $existingCartItem->vat - $existingCartItem->discount) * ($existingCartItem->qty + 1)
            ]);
        } else {
            Cart::create([
                'session_id' => $this->sessionID,
                'product_id' => $product->id,
                'qty' => 1,
                'price' => $product->price,
                'vat' => $product->vat ?? 0,
                'discount' => $product->discount ?? 0,
                'total' => ($product->price + ($product->vat ?? 0) - ($product->discount ?? 0)) * 1,
                'options' => []
            ]);
        }

        $this->dispatch('barcodeScanned');
        $this->dispatch('refreshCart');
        $this->dispatch(
            'success',
            title: __('Product Added'),
            message: __('Product added to cart successfully')
        );
    }

    public function render()
    {
        return view('livewire.pos-barcode-scanner');
    }
}
