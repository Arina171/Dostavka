@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10"> 
            <div class="card">
                <div class="card-header">Редактировать Заказ #{{ $order->id }}</div>

                <div class="card-body">
                    {{-- Форма для обновления заказа --}}
                    <form method="POST" action="{{ route('orders.update', $order->id) }}">
                        @csrf 
                        @method('PUT') 

                        <h5 class="mt-3 mb-3">Основные данные заказа</h5>

                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Пользователь (Клиент)</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Выберите пользователя</option>
                
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('user_id', $order->user_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} (ID: {{ $client->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order_date" class="form-label">Дата Заказа</label>
                            <input type="datetime-local" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', \Carbon\Carbon::parse($order->order_date)->format('Y-m-d\TH:i')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                @php
                                    $statuses = ['В обработке', 'Подтвержден', 'Отправлен', 'Доставлен', 'Отменен'];
                                @endphp
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ old('status', $order->status) == $statusOption ? 'selected' : '' }}>
                                        {{ $statusOption }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total_price" class="form-label">Общая Сумма</label>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price" value="{{ old('total_price', $order->total_price) }}" required readonly>
                            @error('total_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mt-4 mb-3">Товары в заказе</h5>

                        <div id="product-list">
                            @foreach($order->products as $key => $product)
                                <div class="row mb-2 product-item" data-product-id="{{ $product->id }}" data-product-price="{{ $product->price }}">
                                    <div class="col-md-6">
                                        <label class="form-label visually-hidden">Товар</label>
                                        <select class="form-control product-select" disabled>
                                            <option value="{{ $product->id }}" selected>{{ $product->name }} ({{ $product->price }} руб.)</option>
                                        </select>
                                        <input type="hidden" name="products[{{ $key }}][id]" value="{{ $product->id }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label visually-hidden">Количество</label>
                                        <input type="number" class="form-control product-quantity" name="products[{{ $key }}][quantity]" value="{{ old('products.' . $key . '.quantity', $product->pivot->quantity) }}" min="1" required>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-product-btn">Удалить</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-product-btn" class="btn btn-success mt-3 mb-4">Добавить товар</button>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">Обновить Заказ</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="product-row-template">
    <div class="row mb-2 product-item">
        <div class="col-md-6">
            <label class="form-label visually-hidden">Товар</label>
            <select class="form-control product-select" name="products[KEY][id]" required>
                <option value="">Выберите товар</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ $product->price }} руб.)</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label visually-hidden">Количество</label>
            <input type="number" class="form-control product-quantity" name="products[KEY][quantity]" value="1" min="1" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn btn-danger remove-product-btn">Удалить</button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productList = document.getElementById('product-list');
        const addProductBtn = document.getElementById('add-product-btn');
        const productRowTemplate = document.getElementById('product-row-template');
        const totalPriceInput = document.getElementById('total_price');
        let productCounter = {{ count($order->products) }};

        function calculateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.product-item').forEach(itemRow => {
                const quantityInput = itemRow.querySelector('.product-quantity');
                const quantity = parseInt(quantityInput.value);

                let price = 0;
                const hiddenProductIdInput = itemRow.querySelector('input[type="hidden"][name$="[id]"]');
                if (hiddenProductIdInput) {
                    price = parseFloat(itemRow.dataset.productPrice);
                } else {
                    const selectedOption = itemRow.querySelector('select.product-select option:checked');
                    price = parseFloat(selectedOption?.dataset.price);
                }

                if (!isNaN(quantity) && quantity > 0 && !isNaN(price)) {
                    total += price * quantity;
                }
            });
            totalPriceInput.value = total.toFixed(2);
        }

        addProductBtn.addEventListener('click', function () {
            const newRowContent = productRowTemplate.content.cloneNode(true);
            let rowHtml = newRowContent.firstElementChild.outerHTML.replace(/KEY/g, productCounter);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = rowHtml;
            const clonedRow = tempDiv.firstElementChild;

            const selectElement = clonedRow.querySelector('.product-select');
            const quantityInput = clonedRow.querySelector('.product-quantity');
            const removeButton = clonedRow.querySelector('.remove-product-btn');

            selectElement.addEventListener('change', calculateTotalPrice);
            quantityInput.addEventListener('input', calculateTotalPrice);
            removeButton.addEventListener('click', function () {
                this.closest('.product-item').remove();
                calculateTotalPrice();
            });

            productList.appendChild(clonedRow);
            productCounter++;
            calculateTotalPrice();
        });

        // Инициализация слушателей для УЖЕ СУЩЕСТВУЮЩИХ товаров при загрузке страницы.
        document.querySelectorAll('.product-item .product-quantity').forEach(input => {
            input.addEventListener('input', calculateTotalPrice);
        });
        document.querySelectorAll('.product-item .remove-product-btn').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.product-item').remove();
                calculateTotalPrice();
            });
        });

        // Выполняем первоначальный расчет при загрузке страницы.
        calculateTotalPrice();
    });
</script>
@endpush