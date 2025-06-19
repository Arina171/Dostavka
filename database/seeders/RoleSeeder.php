<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; 

class RoleSeeder extends Seeder
{

    public function run(): void
    {
        Role::firstOrCreate(['role_name' => 'admin']);
        Role::firstOrCreate(['role_name' => 'client']);
        Role::firstOrCreate(['role_name' => 'courier']);
    }
}