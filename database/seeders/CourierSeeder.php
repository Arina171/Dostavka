<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Courier; 
use App\Models\User;    

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $alexDriver = User::where('email', 'courier1@example.com')->first();

        if ($alexDriver) {
            Courier::firstOrCreate(
                ['user_id' => $alexDriver->id],
                [
                    'name' => 'Alex Driver',
                    'phone' => $alexDriver->phone, 
                ]
            );
        }
    }
}