<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_kasir', 'id_pelanggan', 'tanggal_pesanan', 'nama_pelanggan','nomor_meja', 'kode_unik', 'status_pesanan', 'metode_pesan','subtotal', 'service', 'pajak','total_harga'])]
class pesanan extends Model{

    protected function casts(): array{
        return ['tanggal_pesanan' => 'datetime'];
    }

    public function kasir(): BelongsTo{
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public function pelanggan(): BelongsTo{
        return $this->belongsTo(User::class, 'id_pelanggan');
    }

    public function detailPesanan(): HasMany{
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }
}
