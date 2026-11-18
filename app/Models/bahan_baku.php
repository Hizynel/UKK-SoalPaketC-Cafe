<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_bahan', 'stock', 'batas_minimun', 'satuan', 'harga_terakhir', 'harga_diperbaharui'])]
class bahan_baku extends Model{
    protected function casts(): array{
        return [
            'harga_diperbaharui' => 'date',
        ];
    }

    public function resep(): HasMany{
        return $this->hasMany(Resep::class, 'id_bahan_baku');
        }
    public function stock(): HasMany{
        return $this->hasMany(stock::class, 'id_bahan_baku');
    }
    public function menipis($query){
        return $query->whereColumn('stock', '<=', 'batas_minimun');
    }
}   
