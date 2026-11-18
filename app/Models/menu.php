<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_menu', 'harga', 'status','kategori'])]
class menu extends Model{

    public function reseps(): HasMany{
        return $this->hasMany(resep::class, 'id_menu');
    }

    public function detailPesanans(): HasMany{
        return $this->hasMany(detail_pesanan::class, 'id_menu');
    }

    public function getHppAttribute(): float{
        return(float) $this->reseps->sum(
            fn ($r) => $r->takaran * ($r->bahanBaku?->harga_terakhir ?? 0)
    );
    }

    public function getAktifAttribute(): bool{
    if (array_key_exists('is_active', $this->attributes)) {
        return (bool) $this->is_active;
    }
    return ($this->attributes['status'] ?? '') === 'tersedia';
    }

    public function getMarginAttribute(): float{
        return(float) $this->harga - $this->hpp;
    }
    
    public function getStatusStokAttribute(): string{
    if ($this->reseps->isEmpty()) {
        return 'Tanpa resep';
    }

    $kurang = $this->reseps->contains(
        fn ($r) => ! $r->bahanBaku || $r->bahanBaku->stock < $r->takaran
    );

    return $kurang ? 'Habis' : 'Tersedia';
}
}
