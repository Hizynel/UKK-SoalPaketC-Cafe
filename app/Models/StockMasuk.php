<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_bahan_baku', 'jumlah', 'tanggal_pembelian'])]
class StockMasuk extends Model
{
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(bahan_baku::class, 'id_bahan_baku');
    }
}
