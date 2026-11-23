<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['nomer_telepon' => '08123456789'],
            [
                'name' => 'Manager Rani',
                'password' => Hash::make('manager123'),
                'role' => 'manajer',
            ],
        );

        User::updateOrCreate(
            ['nomer_telepon' => '081234567899'],
            [
                'name' => 'Rina Kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ],
        );

        User::updateOrCreate(
            ['nomer_telepon' => '081234567890'],
            [
                'name' => 'Rina Wulandari',
                'password' => Hash::make('pelanggan123'),
                'role' => 'pelanggan',
                'must_change_password' => false,
            ],
        );
    }
}
