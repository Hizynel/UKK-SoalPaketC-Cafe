<?php

namespace App\Filament\Resources\BahanBakus;

use App\Filament\Resources\BahanBakus\Pages\CreateBahanBaku;
use App\Filament\Resources\BahanBakus\Pages\EditBahanBaku;
use App\Filament\Resources\BahanBakus\Pages\ListBahanBakus;
use App\Filament\Resources\BahanBakus\Pages\ViewBahanBaku;
use App\Filament\Resources\BahanBakus\Schemas\BahanBakuForm;
use App\Filament\Resources\BahanBakus\Schemas\BahanBakuInfolist;
use App\Filament\Resources\BahanBakus\Tables\BahanBakusTable;
use App\Models\bahan_baku;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BahanBakuResource extends Resource
{
    protected static ?string $model = bahan_baku::class;

    protected static ?string $pluralLabel = 'Bahan Baku';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'bahan_baku';

    public static function form(Schema $schema): Schema
    {
        return BahanBakuForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BahanBakuInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BahanBakusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBahanBakus::route('/'),
            'create' => CreateBahanBaku::route('/create'),
            'view' => ViewBahanBaku::route('/{record}'),
            'edit' => EditBahanBaku::route('/{record}/edit'),
        ];
    }
}
