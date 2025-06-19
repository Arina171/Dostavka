<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment; 
use App\Models\Order;  
use Carbon\Carbon;     

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $testOrder = Order::first(); 

        if ($testOrder) {
            Payment::firstOrCreate(
                ['order_id' => $testOrder->id, 'payment_status' => 'pending'],
                [
                    'amount' => $testOrder->total_price,
                    'payment_type' => 'card',
                    'payment_date' => Carbon::now(),
                ]
            );
        }
    }
}