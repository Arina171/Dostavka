<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр Заказа #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Заказ #{{ $order->id }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>
        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning mb-3">Редактировать Заказ</a>

        <div class="card mb-3">
            <div class="card-header">
                Информация о Заказе
            </div>
            <div class="card-body">
                <p><strong>ID Заказа:</strong> {{ $order->id }}</p>
                <p><strong>Клиент:</strong> {{ $order->user->name }} {{ $order->user->surname }} ({{ $order->user->email }})</p>
                <p><strong>Дата Заказа:</strong> {{ $order->order_date->format('d.m.Y H:i') }}</p>
                <p><strong>Статус:</strong> {{ $order->status }}</p>
                <p><strong>Общая Стоимость:</strong> {{ number_format($order->total_price, 2) }} руб.</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Товары в Заказе
            </div>
            <div class="card-body">
                @if ($order->products->isEmpty())
                    <p>В этом заказе нет товаров.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Название Товара</th>
                                <th>Количество</th>
                                <th>Цена на момент заказа</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>{{ number_format($product->pivot->price_at_order, 2) }} руб.</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Информация о Доставке
            </div>
            <div class="card-body">
                @if ($order->delivery)
                    <p><strong>Метод Доставки:</strong> {{ $order->delivery->delivery_method }}</p>
                    <p><strong>Адрес:</strong> {{ $order->delivery->delivery_address }}</p>
                    <p><strong>Планируемая дата:</strong> {{ $order->delivery->delivery_date?->format('d.m.Y H:i') ?? 'Не указана' }}</p>
                    <p><strong>Статус Доставки:</strong> {{ $order->delivery->delivery_status }}</p>
                    <p><strong>Курьер:</strong> {{ $order->delivery->courier?->name ?? 'Не назначен' }}</p>
                @else
                    <p>Информация о доставке отсутствует.</p>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                Информация о Платежах
            </div>
            <div class="card-body">
                @if ($order->payments->isEmpty())
                    <p>Платежи по этому заказу отсутствуют.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Сумма</th>
                                <th>Тип Оплаты</th>
                                <th>Статус Платежа</th>
                                <th>Дата Платежа</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->payments as $payment)
                                <tr>
                                    <td>{{ number_format($payment->amount, 2) }} руб.</td>
                                    <td>{{ $payment->payment_type }}</td>
                                    <td>{{ $payment->payment_status }}</td>
                                    <td>{{ $payment->payment_date->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
</body>
</html>