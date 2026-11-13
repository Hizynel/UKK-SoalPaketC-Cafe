<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[fillable(['id_pesanan', 'id_menu', 'jumlah', 'subtotal'])]
class detail_pesanan extends Model{
    public function pesanan(): BelongsTo{
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function menu(): BelongsTo{
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}
