<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\User;
use App\Models\UserCanteenRole;
use Illuminate\Database\Seeder;

class AdminCanteenManagerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@kantin.test')->first();
        $canteen = Canteen::where('code', 'KTN-PUSAT')->first();
        if (! $admin || ! $canteen) {
            $this->command?->warn('Akun admin atau kantin demo belum ada. Jalankan seeder demo terlebih dahulu.');

            return;
        }

        UserCanteenRole::firstOrCreate([
            'user_id' => $admin->id,
            'canteen_id' => $canteen->id,
            'role' => 'manager',
        ]);
    }
}
