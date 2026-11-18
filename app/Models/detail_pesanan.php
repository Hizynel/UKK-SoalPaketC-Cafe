<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'id_menu', 'nama_menu', 'jumlah', 'harga_satuan', 'subtotal'])]
class detail_pesanan extends Model{
    public function pesanan(): BelongsTo{
        return $this->belongsTo(pesanan::class, 'id_pesanan');
    }

    public function menu(): BelongsTo{
        return $this->belongsTo(menu::class, 'id_menu');
    }
}
