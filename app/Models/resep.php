<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['id_menu', 'id_bahan_baku', 'takaran'])]
class resep extends Model
{
    public function menu(): BelongsTo{
        return $this->belongsTo(menu::class, 'id_menu');
    }

    public function bahanBaku(): BelongsTo{
        return $this->belongsTo(bahan_baku::class, 'id_bahan_baku');
    }
}
