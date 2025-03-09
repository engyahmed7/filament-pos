<?php

namespace App\Http\Livewire;

use Livewire\Component;
use TomatoPHP\FilamentEcommerce\Models\Cart;
use TomatoPHP\FilamentEcommerce\Models\Product;
use Illuminate\Support\Facades\Session;

class CustomPOS extends Component
{
    public $barcode;
    public $sessionID;

    public function mount()
    {
        $this->sessionID = Session::getId();
    }

    public function scanBarcode()
    {
        $barcode = trim($this->barcode);
        if (!$barcode) return;

        $product = Product::where('barcode', $barcode)->first();
        if (!$product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        $cartItem = Cart::where('session_id', $this->sessionID)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('qty');
        } else {
            Cart::create([
                'session_id' => $this->sessionID,
                'product_id' => $product->id,
                'qty' => 1,
                'price' => $product->price,
                'total' => $product->price,
            ]);
        }

        $this->barcode = '';
        session()->flash('success', "{$product->name} added.");
        $this->emit('cartUpdated');
    }

    public function render()
    {
        return view('livewire.custom-pos', [
            'cart' => Cart::where('session_id', $this->sessionID)->get(),
        ]);
    }
}
