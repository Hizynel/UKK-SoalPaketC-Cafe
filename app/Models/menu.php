<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_menu', 'nama_menu', 'harga', 'status', 'kategori', 'is_active'])]
class menu extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function reseps(): HasMany
    {
        return $this->hasMany(resep::class, 'id_menu');
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(detail_pesanan::class, 'id_menu');
    }

    public function getHppAttribute(): float
    {
        return (float) $this->reseps->sum(
            fn ($r) => $r->takaran * ($r->bahanBaku?->harga_terakhir ?? 0)
        );
    }

    public function getAktifAttribute(): bool
    {
        if (array_key_exists('is_active', $this->attributes)) {
            return (bool) $this->is_active;
        }

        return ($this->attributes['status'] ?? '') === 'tersedia';
    }

    public function getMarginAttribute(): float
    {
        return (float) $this->harga - $this->hpp;
    }

    public function getStatusStokAttribute(): string
    {
        if (! $this->aktif) {
            return 'Nonaktif';
        }

        $reseps = $this->relationLoaded('reseps')
            ? $this->reseps
            : $this->reseps()->with('bahanBaku')->get();

        if ($reseps->isEmpty()) {
            return 'Tanpa resep';
        }

        $kebutuhan = $reseps->groupBy('id_bahan_baku')->map(
            fn ($baris) => $baris->sum('takaran')
        );

        foreach ($kebutuhan as $idBahan => $takaran) {
            $bahan = $reseps->firstWhere('id_bahan_baku', $idBahan)?->bahanBaku;

            if (! $bahan || (float) $bahan->stock < (float) $takaran) {
                return 'Habis';
            }
        }

        return 'Tersedia';
    }
}
