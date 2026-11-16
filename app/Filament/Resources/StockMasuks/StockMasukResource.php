<?php

namespace App\Filament\Resources\StockMasuks;

use App\Filament\Resources\StockMasuks\Pages\CreateStockMasuk;
use App\Filament\Resources\StockMasuks\Pages\EditStockMasuk;
use App\Filament\Resources\StockMasuks\Pages\ListStockMasuks;
use App\Filament\Resources\StockMasuks\Pages\ViewStockMasuk;
use App\Filament\Resources\StockMasuks\Schemas\StockMasukForm;
use App\Filament\Resources\StockMasuks\Schemas\StockMasukInfolist;
use App\Filament\Resources\StockMasuks\Tables\StockMasuksTable;
use App\Models\StockMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;


class StockMasukResource extends Resource
{
    protected static ?string $model = StockMasuk::class;

    protected static ?string $pluralLabel = 'Stock Masuk';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'stock_masuk';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(
            [Select::make('id_bahan_baku')->label('Bahan')->relationship('bahanBaku', 'nama_bahan')->searchable()->preload()->required(),
            TextInput::make('jumlah')->numeric()->minValue(0.01)->required(),
            DateTimePicker::make('tanggal_pembelian')->default(now())->required(),
    ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockMasukInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tanggal_pembelian')->dateTime('d M Y H:i')->sortable(),
            TextColumn::make('bahanBaku.nama_bahan')->label('Bahan')->searchable(),
            TextColumn::make('jumlah'),
        ])->defaultSort('tanggal_pembelian', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canEdit(Model $record): bool { return false; }

    public static function canDelete(Model $record): bool { return false; }
    
    public static function getPages(): array
    {
        return [
            'index' => ListStockMasuks::route('/'),
            'create' => CreateStockMasuk::route('/create'),
            // 'view' => ViewStockMasuk::route('/{record}'),
            // 'edit' => EditStockMasuk::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
