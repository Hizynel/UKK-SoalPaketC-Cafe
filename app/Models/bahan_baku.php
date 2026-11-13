<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[fillable(['nama_bahan', 'stock', 'batas_minimun', 'satuan'])]
class bahan_baku extends Model{
public function resep(): HasMany{
        return $this->hasMany(Resep::class, 'id_bahan_baku');
    }
}
