<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['nomer_telepon' => '08123456789'],
            [
                'name' => 'Manager Rani',
                'password' => 'manager123',
                'role' => 'manajer',
            ],
        );

        User::updateOrCreate(
            ['nomer_telepon' => '081234567899'],
            [
                'name' => 'Kasir Andi',
                'password' => 'kasir123',
                'role' => 'kasir',
            ],
        );

        User::updateOrCreate(
            ['nomer_telepon' => '081234567890'],
            [
                'name' => 'Dedi',
                'password' => 'Dedi123',
                'role' => 'pelanggan',
            ],
        );
    }
}