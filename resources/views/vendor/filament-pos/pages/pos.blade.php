<x-filament-panels::page>
    @livewire(\TomatoPHP\FilamentPos\Filament\Widgets\POSStateWidget::class)

    <div class="flex justify-center mt-4">
        <button id="startScanner">
            Scan your barcode
        </button>
    </div>

    <div id="camera-container" class="hidden mt-4">
        <div id="camera" style="width: 300px; height:200px;"></div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #startScanner {
            display: inline-block;
            background-color: rgb(217 119 6);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script>
        let scannerActive = false;

        document.getElementById('startScanner').addEventListener('click', function() {
            if (!scannerActive) {
                startScanner();
                this.innerText = 'Stop Scanner';
                document.getElementById('camera-container').classList.remove('hidden');
            } else {
                stopScanner();
                this.innerText = 'Start Scanner';
                document.getElementById('camera-container').classList.add('hidden');
            }
            scannerActive = !scannerActive;
        });

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#camera'),
                    constraints: {
                        facingMode: "environment",
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                },
                decoder: {
                    readers: ['code_128_reader', 'ean_reader', 'upc_reader']
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error(err);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                const barcode = result.codeResult.code;
                console.log("Scanned Barcode:", barcode);

                fetch(`/pos/scan/${barcode}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data);
                            Livewire.dispatch('refreshCart');
                        }
                    });

                stopScanner();
            });
        }

        function stopScanner() {
            Quagga.stop();
            document.getElementById('startScanner').innerText = 'Scan your barcode';
            document.getElementById('camera-container').classList.add('hidden');
            scannerActive = false;
            // window.location.reload();

        }
    </script>

    <div class="grid sm:grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            {{ $this->table }}
        </div>

        <div class="flex flex-col gap-4">
            <x-filament::section :heading="trans('filament-pos::messages.view.cart')">
                @php $cart = \TomatoPHP\FilamentEcommerce\Models\Cart::query()->where('session_id', $this->sessionID)
                ->get() @endphp
                @if(count($cart))
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($cart as $item)
                    <div class="flex justify-between items-center py-2">
                        <div>
                            <p>{{ $item->product->name }}</p>
                            <p>{{ $item->qty }} * {{ number_format(($item->price+$item->vat)-$item->discount, 2) }}<small>{{ setting('site_currency') }}</small></p>
                        </div>
                        <div>
                            <x-filament::icon-button :tooltip="trans('filament-pos::messages.view.remove')" icon="heroicon-s-trash" color="danger" wire:click="removeFromCart({{ $item->id }})"></x-filament::icon-button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-2">
                    <x-filament::button color="danger" wire:click="clearCart">{{ trans('filament-pos::messages.view.clear') }}</x-filament::button>
                </div>
                @else
                <div class="text-center flex justify-center items-center flex-col h-full">
                    <div class="flex justify-center items-center flex-col gap-2">
                        <x-heroicon-c-shopping-cart class="w-8 h-8" />
                        <p>{{ trans('filament-pos::messages.view.empty') }}</p>
                    </div>
                </div>
                @endif
            </x-filament::section>

            @if(count($cart))
            <x-filament::section :heading="trans('filament-pos::messages.view.totals')">
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    <div class="flex justify-between items-center py-2">
                        <p class="font-bold">{{ trans('filament-pos::messages.view.subtotal') }}</p>
                        <p>{{ number_format($cart->sum(function ($item){
                                return $item->qty * $item->price;
                            }), 2) }}<small>{{ setting('site_currency') }}</small></p>
                    </div>
                    <div class="flex justify-between items-center py-2 text-danger-600">
                        <p class="font-bold">{{ trans('filament-pos::messages.view.discount') }}</p>
                        <p>{{ number_format($cart->sum(function ($item){
                                return $item->qty * $item->discount;
                            }), 2) }}<small>{{ setting('site_currency') }}</small></p>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <p class="font-bold">{{ trans('filament-pos::messages.view.vat') }}</p>
                        <p>{{ number_format($cart->sum(function ($item){
                                return $item->qty * $item->vat;
                            }), 2) }}<small>{{ setting('site_currency') }}</small></p>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <p class="font-bold">{{ trans('filament-pos::messages.view.total') }}</p>
                        <p class="font-bold">{{ number_format($cart->sum(function ($item){
                                return $item->qty * $item->total;
                            }), 2) }}<small>{{ setting('site_currency') }}</small></p>
                    </div>
                </div>
                <div class="mt-2">
                    {{ ($this->checkoutAction)(['total' => $cart->sum(fn($item) => ($item->qty * $item->total))]) }}
                </div>
            </x-filament::section>
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>