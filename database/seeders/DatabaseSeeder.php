<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Manager Rani',
            'nomer_telepon' => '08123456789',
            'password' => ('manager123'),
            'role' => 'manajer'
        ]);
        User::create([
            'name' => 'Kasir Andi',
            'nomer_telepon' => '081234567899',
            'password' => ('kasir123'),
            'role' => 'kasir'
        ]);

        $this->call(BahanBakuSeeder::class);
    }
}
