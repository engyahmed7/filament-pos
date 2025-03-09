<div>
    <!-- Barcode Scanner Input -->
    <input type="text" wire:model.defer="barcode" wire:keydown.enter="scanBarcode"
        placeholder="Scan barcode..." autofocus class="w-full p-2 border rounded-md" />

    @if (session()->has('success'))
    <div class="text-green-500">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
    <div class="text-red-500">{{ session('error') }}</div>
    @endif

    <!-- Cart Items -->
    <x-filament::section heading="Cart">
        @foreach ($cart as $item)
        <div class="flex justify-between items-center py-2">
            <p>{{ $item->product->name }} - {{ $item->qty }}</p>
            <button wire:click="removeItem({{ $item->id }})">❌</button>
        </div>
        @endforeach
    </x-filament::section>
</div>