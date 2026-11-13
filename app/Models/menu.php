<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[fillable(['nama_menu', 'harga', 'status'])]
class menu extends Model{

    public function reseps(): HasMany{
        return $this->hasMany(resep::class, 'id_menu');
    }
    public function detailPesanans(): HasMany{
        return $this->hasMany(detailPesanan::class, 'id_menu');
    }
}
