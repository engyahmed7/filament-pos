<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\ProductExporter;
use Filament\Tables\Actions\ExportAction;
use TomatoPHP\FilamentEcommerce\Filament\Resources\ProductResource as ResourcesProductResource;

class ProductResource extends ResourcesProductResource
{
    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        return $table
            ->headerActions([
                Tables\Actions\Action::make('print_all')
                    ->label('Print All Products')
                    ->icon('heroicon-s-printer')
                    ->openUrlInNewTab()
                    ->url(route('products.print-all'))
                    ->iconButton(),

                ExportAction::make()
                    ->exporter(ProductExporter::class),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('customAction')
                    ->icon('heroicon-s-printer')
                    ->openUrlInNewTab()
                    ->url(fn(\TomatoPHP\FilamentEcommerce\Models\Product $record): string => route('product.print', $record))
                    ->iconButton(),
                ...$table->getActions()
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportAction::make()
                        ->exporter(ProductExporter::class)

                ]),
            ]);
    }
}
