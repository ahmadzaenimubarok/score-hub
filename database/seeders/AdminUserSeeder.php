<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed akun admin.
 *
 * Cara pakai:
 *   ADMIN_PASSWORD='passwordkuat' php artisan db:seed --class=AdminUserSeeder
 *
 * Jika ADMIN_PASSWORD tidak di-set, password acak dibuat & ditampilkan sekali.
 * Idempotent — aman dijalankan ulang (updateOrCreate by email).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@scorehub.my.id');
        $password = env('ADMIN_PASSWORD', Str::random(16));

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => $password,
            ]
        );

        if (! env('ADMIN_PASSWORD')) {
            $this->command->info("Admin {$email} dibuat. Password: {$password}");
        }
    }
}
