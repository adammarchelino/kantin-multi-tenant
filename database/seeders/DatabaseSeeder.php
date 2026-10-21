<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrNew(['email' => 'test@example.com'])
            ->forceFill([
                'name' => 'Test User', 'email_verified_at' => now(),
                'password' => Hash::make('password'), 'status' => 'active',
            ])->save();

        foreach ([
            ['name' => 'Admin Kantin', 'email' => 'admin@kantin.test', 'role' => 'admin'],
            ['name' => 'Operator Tenant', 'email' => 'tenant@kantin.test', 'role' => 'tenant'],
        ] as $account) {
            User::firstOrNew(['email' => $account['email']])
                ->forceFill([
                    ...$account,
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
                ->save();
        }

        $this->call(DemoCanteenSeeder::class);

    }
}
