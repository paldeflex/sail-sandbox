<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            User::factory()->create([
                'name' => 'admin',
                'email' => 'paldeflex@gmail.com',
                'password' => Hash::make('123456'),
                'is_admin' => true,
            ]);
        }

        User::factory(10)->create();
    }
}
