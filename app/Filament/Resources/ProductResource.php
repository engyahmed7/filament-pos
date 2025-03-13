<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Actions\Action;
use TomatoPHP\FilamentEcommerce\Filament\Resources\ProductResource as ResourcesProductResource;

class ProductResource extends ResourcesProductResource
{
    public static function table(Tables\Table $table): Tables\Table
    {
        dd('test');
        return parent::table($table)
            ->headerActions([
                Action::make('print_all')
                    ->label('Print All Products')
                    ->icon('heroicon-s-printer')
                    ->openUrlInNewTab()
                    ->url(route('products.print-all'))
                    ->iconButton(),

                Tables\Actions\ExportAction::make(),
            ]);
    }
}
