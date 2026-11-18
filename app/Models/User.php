<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'nomer_telepon', 'password','role','must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string{
        return $this->nama ?? 'manager';
    }

    public function canAccessPanel(Panel $panel): bool{
        return $this->role === 'manajer';
    }

    public function pesananSebagaiKasir(): HasMany{
        return $this->hasMany(pesanan::class, 'id_kasir');
    }

    public function pesananSebagaiPelanggan(): HasMany{
        return $this->hasMany(pesanan::class, 'id_pelanggan');
    }
}
