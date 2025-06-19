@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Создать Новый Заказ</h1>

    <div class="bg-white shadow-lg rounded-lg p-8 max-w-4xl mx-auto">

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Упс!</strong>
                <span class="block sm:inline">Есть проблемы с вашим вводом:</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">
                    Клиент:
                </label>
                <select name="user_id" id="user_id"
                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('user_id') border-red-500 @enderror">
                    <option value="">Выберите клиента</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} {{ $client->surname }} ({{ $client->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="order_date" class="block text-gray-700 text-sm font-bold mb-2">
                    Дата Заказа:
                </label>
                <input type="date" name="order_date" id="order_date" value="{{ old('order_date', date('Y-m-d')) }}"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('order_date') border-red-500 @enderror">
                @error('order_date')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">
                    Статус:
                </label>
                <select name="status" id="status"
                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('status') border-red-500 @enderror">
                    <option value="В ожидании" {{ old('status') == 'В ожидании' ? 'selected' : '' }}>В ожидании</option>
                    <option value="В процессе" {{ old('status') == 'В процессе' ? 'selected' : '' }}>В процессе</option>
                    <option value="Завершено" {{ old('status') == 'Завершено' ? 'selected' : '' }}>Завершено</option>
                    <option value="Отменено" {{ old('status') == 'Отменено' ? 'selected' : '' }}>Отменено</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Продукты Заказа</h3>
            <div id="product-list-container" class="space-y-4 mb-6 p-4 border border-gray-200 rounded-md bg-gray-50">
                
                @if(old('products'))
                    @foreach(old('products') as $index => $oldProduct)
                        <div class="product-item flex flex-wrap items-center gap-4 p-4 border border-gray-300 rounded-md bg-white shadow-sm">
                            <div class="flex-1 min-w-[200px]">
                                <label for="products_{{ $index }}_id" class="block text-gray-700 text-xs font-bold mb-1">
                                    Продукт:
                                </label>
                                <select name="products[{{ $index }}][id]" id="products_{{ $index }}_id"
                                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('products.' . $index . '.id') border-red-500 @enderror">
                                    <option value="">Выберите продукт</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $oldProduct['id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ number_format($product->price, 2, ',', ' ') }} ₽)
                                        </option>
                                    @endforeach
                                </select>
                                @error('products.' . $index . '.id')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-24">
                                <label for="products_{{ $index }}_quantity" class="block text-gray-700 text-xs font-bold mb-1">
                                    Кол-во:
                                </label>
                                <input type="number" name="products[{{ $index }}][quantity]" id="products_{{ $index }}_quantity" value="{{ $oldProduct['quantity'] }}" min="1"
                                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('products.' . $index . '.quantity') border-red-500 @enderror">
                                @error('products.' . $index . '.quantity')
                                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex-shrink-0 mt-5">
                                <button type="button" onclick="removeProduct(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-200">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" onclick="addProduct()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md mb-6 transition duration-200">
                Добавить Продукт
            </button>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
                    Создать Заказ
                </button>
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
                    Назад к списку
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    let productIndex = {{ old('products') ? count(old('products')) : 0 }}; // Отслеживаем индекс для уникальных имен полей

    function addProduct() {
        const container = document.getElementById('product-list-container');
        const productItem = document.createElement('div');
        productItem.classList.add('product-item', 'flex', 'flex-wrap', 'items-center', 'gap-4', 'p-4', 'border', 'border-gray-300', 'rounded-md', 'bg-white', 'shadow-sm');

        productItem.innerHTML = `
            <div class="flex-1 min-w-[200px]">
                <label for="products_${productIndex}_id" class="block text-gray-700 text-xs font-bold mb-1">
                    Продукт:
                </label>
                <select name="products[${productIndex}][id]" id="products_${productIndex}_id"
                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                    <option value="">Выберите продукт</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->price, 2, ',', ' ') }} ₽)</option>
                    @endforeach
                </select>
            </div>
            <div class="w-24">
                <label for="products_${productIndex}_quantity" class="block text-gray-700 text-xs font-bold mb-1">
                    Кол-во:
                </label>
                <input type="number" name="products[${productIndex}][quantity]" id="products_${productIndex}_quantity" value="1" min="1"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
            </div>
            <div class="flex-shrink-0 mt-5">
                <button type="button" onclick="removeProduct(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-200">
                    Удалить
                </button>
            </div>
        `;

        container.appendChild(productItem);
        productIndex++; 
    }

    function removeProduct(button) {
        button.closest('.product-item').remove();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (productIndex === 0 && document.getElementById('product-list-container').children.length === 0) {
            addProduct();
        }
    });
</script>
@endsection
