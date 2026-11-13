<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Iluminate\Database\Eloquent\Relations\BelongsTo;

#[fillable(['id_menu', 'id_bahan_baku', 'takaran'])]
class resep extends Model
{
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(bahan_baku::class, 'id_bahan_baku');
    }
}
