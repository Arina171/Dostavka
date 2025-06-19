<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; 
use App\Models\Role; 
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('role_name', 'admin')->first();
        $clientRole = Role::where('role_name', 'client')->first();
        $courierRole = Role::where('role_name', 'courier')->first();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'surname' => 'System',
                'password' => Hash::make('password'), 
                'phone' => '1112223344',
                'role_id' => $adminRole->id ?? null,
            ]
        );

    }
}