<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon; 

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем всех клиентов и продукты
        $clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->get();

        $products = Product::all();

        if ($clients->isEmpty()) {
            $this->command->warn('No clients found. Skipping OrderSeeder.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Skipping OrderSeeder.');
            return;
        }

        // Создаем несколько тестовых заказов
        for ($i = 0; $i < 5; $i++) {
            $client = $clients->random(); 
            $orderDate = Carbon::now()->subDays(rand(1, 30)); 

            $order = Order::create([
                'user_id' => $client->id,
                'order_date' => $orderDate,
                'status' => $this->getRandomOrderStatus(), 
                'total_price' => 0, 
            ]);

            $selectedProducts = $products->shuffle()->take(rand(1, min(3, $products->count())));
            $currentTotalPrice = 0;
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3); 
                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price_at_order' => $product->price,
                ]);
                $currentTotalPrice += $product->price * $quantity;
            }

            $order->update(['total_price' => $currentTotalPrice]);
        }
    }

    private function getRandomOrderStatus(): string
    {
        $statuses = ['В ожидании', 'В процессе', 'Завершено', 'Отменено'];
        return $statuses[array_rand($statuses)];
    }
}