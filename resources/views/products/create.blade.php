@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Добавить Новый Товар</h1>

    <div class="bg-white shadow-lg rounded-lg p-8 max-w-xl mx-auto">
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

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                    Название:
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">
                    Slug:
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('slug') border-red-500 @enderror"
                       required>
                @error('slug')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                    Описание:
                </label>
                <textarea name="description" id="description" rows="4"
                          class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">
                    Цена:
                </label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0.01"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('price') border-red-500 @enderror"
                       required>
                @error('price')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="stock_quantity" class="block text-gray-700 text-sm font-bold mb-2">
                    Количество на складе:
                </label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') }}" min="0"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('stock_quantity') border-red-500 @enderror"
                       required>
                @error('stock_quantity')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">
                    Категория:
                </label>
                <select name="category_id" id="category_id"
                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Выберите категорию (опционально)</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="manufacturer_id" class="block text-gray-700 text-sm font-bold mb-2">
                    Производитель:
                </label>
                <select name="manufacturer_id" id="manufacturer_id"
                        class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('manufacturer_id') border-red-500 @enderror">
                    <option value="">Выберите производителя (опционально)</option>
                    @foreach($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer->id }}" {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                            {{ $manufacturer->name }}
                        </option>
                    @endforeach
                </select>
                @error('manufacturer_id')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="image_url" class="block text-gray-700 text-sm font-bold mb-2">
                    URL Изображения:
                </label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}"
                       class="shadow border rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 @error('image_url') border-red-500 @enderror">
                @error('image_url')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
                    Добавить Товар
                </button>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
                    Назад к списку
                </a>
            </div>
        </form>
    </div>
</div>
@endsection