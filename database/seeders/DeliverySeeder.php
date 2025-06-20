<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Delivery; 
use App\Models\Order;    
use App\Models\Courier;  
use Carbon\Carbon;       

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $testOrder = Order::where('status', 'pending')->first(); 
        $alexCourier = Courier::where('name', 'Alex Driver')->first();

        if ($testOrder && $alexCourier) {
            Delivery::firstOrCreate(
                ['order_id' => $testOrder->id],
                [
                    'delivery_method' => 'courier',
                    'delivery_address' => 'ул. 1 мая д.20 кв.45',
                    'delivery_date' => Carbon::now()->addDays(2), 
                    'delivery_status' => 'assigned',
                    'courier_id' => $alexCourier->id,
                ]
            );
        }
    }
}