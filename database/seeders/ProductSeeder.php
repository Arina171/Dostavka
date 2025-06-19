<?php

    namespace Database\Seeders;

    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    use App\Models\Product;
    use App\Models\Category; 
    use App\Models\Manufacturer;
    use Illuminate\Support\Str; 

    class ProductSeeder extends Seeder
    {
        public function run(): void
        {
            // Получаем существующие категории и производителей
            $categories = Category::all();
            $manufacturers = Manufacturer::all();

            if ($categories->isEmpty()) {
                $this->command->warn('No categories found. Please run CategorySeeder first.');
            }
            if ($manufacturers->isEmpty()) {
                $this->command->warn('No manufacturers found. Please run ManufacturerSeeder first.');
            }
            
            // Если после предупреждений все еще нет данных, выйти.
            if ($categories->isEmpty() || $manufacturers->isEmpty()) {
                return;
            }

            // Создаем несколько тестовых продуктов
            Product::firstOrCreate(
                ['name' => 'Смартфон XYZ'],
                [
                    'slug' => Str::slug('Смартфон XYZ'), 
                    'description' => 'Мощный смартфон с отличной камерой.',
                    'price' => 750.00,
                    'stock_quantity' => 50,
                    'category_id' => $categories->random()->id,
                    'manufacturer_id' => $manufacturers->random()->id,
                ]
            );

            Product::firstOrCreate(
                ['name' => 'Ноутбук ProBook 15'],
                [
                    'slug' => Str::slug('Ноутбук ProBook 15'),
                    'description' => 'Легкий и производительный ноутбук для работы.',
                    'price' => 1200.00,
                    'stock_quantity' => 30,
                    'category_id' => $categories->random()->id,
                    'manufacturer_id' => $manufacturers->random()->id,
                ]
            );

            Product::firstOrCreate(
                ['name' => 'Беспроводные наушники SoundMax'],
                [
                    'slug' => Str::slug('Беспроводные наушники SoundMax'), 
                    'description' => 'Высококачественные наушники с шумоподавлением.',
                    'price' => 150.00,
                    'stock_quantity' => 100,
                    'category_id' => $categories->random()->id,
                    'manufacturer_id' => $manufacturers->random()->id,
                ]
            );

        }
    }