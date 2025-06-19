<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,     
            UserSeeder::class,     
            ProductSeeder::class,  
            OrderSeeder::class,    
            CourierSeeder::class,  
            DeliverySeeder::class, 
            PaymentSeeder::class,  
        ]);
    }
}