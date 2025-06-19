@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Список Сравнения</h1>

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
    @if (session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('info') }}
        </div>
    @endif

    @if($comparisonList && $comparisonList->items->isNotEmpty())
        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Изображение</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Производитель</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($comparisonList->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ $item->product->image_url ?? 'https://placehold.co/100x75/cccccc/333333?text=Нет+Изображения' }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-20 h-15 object-cover rounded-md"
                                     onerror="this.onerror=null;this.src='https://placehold.co/100x75/cccccc/333333?text=Ошибка';" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('products.show', $item->product->id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $item->product->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ Str::limit($item->product->description, 100) }} {{-- Ограничиваем длину описания --}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->product->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->product->manufacturer->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg font-semibold text-gray-800">
                                {{ number_format($item->product->price, 2, ',', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('comparison.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Удалить
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6 text-right">
                <form action="{{ route('comparison.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-200">
                        Очистить список
                    </button>
                </form>
            </div>
        </div>
    @else

        <div class="bg-white shadow-md rounded-lg p-6 text-center text-gray-600 text-lg">
            <p>Список сравнения пуст.</p>
            <p class="mt-2">Перейдите на <a href="{{ route('home') }}" class="text-blue-600 hover:underline">главную страницу</a>, чтобы добавить товары.</p>
        </div>
    @endif
</div>
@endsection
