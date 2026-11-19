<?php

namespace App\Filament\Resources\Reseps;

use App\Filament\Resources\Reseps\Pages\CreateResep;
use App\Filament\Resources\Reseps\Pages\EditResep;
use App\Filament\Resources\Reseps\Pages\ListReseps;
use App\Models\resep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResepResource extends Resource
{
    protected static ?string $model = resep::class;

    protected static ?string $modelLabel = 'Resep';

    protected static ?string $pluralLabel = 'Resep';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\Reseps\Schemas\ResepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Reseps\Tables\ResepsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReseps::route('/'),
            'create' => CreateResep::route('/create'),
            'edit' => EditResep::route('/{record}/edit'),
        ];
    }
}
