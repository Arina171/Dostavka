@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-6">
            Добро пожаловать в Систему Управления Доставками!
        </h1>
        <p class="text-xl text-center text-gray-700 mb-12">
            Здесь вы можете найти наши лучшие предложения по продуктам для заказа с доставкой.
        </p>

        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Наши Рекомендации</h2>

        @if($recommendedProducts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                @foreach($recommendedProducts as $product)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform duration-300 hover:scale-105">
                        <img src="{{ $product->image_url ?? 'https://placehold.co/400x300/cccccc/333333?text=Нет+Изображения' }}"
                             alt="{{ $product->name }}"
                             class="w-full h-48 object-cover">

                        <div class="p-6">
                            
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ route('products.show', $product->id) }}" class="hover:text-blue-600 transition-colors duration-200">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            @if($product->category)
                                <p class="text-gray-600 text-sm mb-1">
                                    <strong>Категория:</strong> {{ $product->category->name }}
                                </p>
                            @endif

                            @if($product->manufacturer)
                                <p class="text-gray-600 text-sm mb-3">
                                    <strong>Производитель:</strong> {{ $product->manufacturer->name }}
                                </p>
                            @endif

                            <p class="text-2xl font-bold text-blue-700 mt-4">{{ number_format($product->price, 2, ',', ' ') }} ₽</p>

                            <form action="{{ route('comparison.toggle') }}" method="POST" class="mt-4">
                                @csrf {{-- Токен CSRF для защиты от подделки межсайтовых запросов. --}}
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="action" value="add"> {{-- Указываем действие "добавить". --}}
                                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                                    Добавить в сравнение
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-600 text-lg">
                Пока нет рекомендованных продуктов для доставки. Пожалуйста, добавьте их в административной панели.
            </p>
        @endif
    </div>
@endsection