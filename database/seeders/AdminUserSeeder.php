<?php

namespace Database\Seeders;

use App\Models\UserModel;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = UserModel::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Nick Howarth'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'changeme')),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $user->assignRole('super-admin');
    }
}
