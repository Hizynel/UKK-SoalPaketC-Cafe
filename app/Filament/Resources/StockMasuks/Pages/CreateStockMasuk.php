<?php

namespace App\Filament\Resources\StockMasuks\Pages;

use App\Filament\Resources\StockMasuks\StockMasukResource;
use App\Models\bahan_baku;
use App\Models\StockMasuk;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStockMasuk extends CreateRecord
{
    protected static string $resource = StockMasukResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $bahan = bahan_baku::lockForUpdate()->findOrFail($data['id_bahan_baku']);
            $record = StockMasuk::create($data);
            $bahan->increment('stock', $data['jumlah']);
            return $record;
        });
    }
}