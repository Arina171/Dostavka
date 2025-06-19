<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запланировать Доставку</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Запланировать Новую Доставку</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('deliveries.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="order_id" class="form-label">Заказ:</label>
                <select class="form-control" id="order_id" name="order_id" required>
                    <option value="">Выберите заказ</option>
                    @foreach ($ordersWithoutDelivery as $order)
                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            Заказ #{{ $order->id }} (Клиент: {{ $order->user->name }} {{ $order->user->surname }}, Сумма: {{ number_format($order->total_price, 2) }} руб.)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="delivery_method" class="form-label">Метод Доставки:</label>
                <input type="text" class="form-control" id="delivery_method" name="delivery_method" value="{{ old('delivery_method', 'Курьерская доставка') }}" required>
            </div>
            <div class="mb-3">
                <label for="delivery_address" class="form-label">Адрес Доставки:</label>
                <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="{{ old('delivery_address') }}" required>
            </div>
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Планируемая Дата Доставки:</label>
                <input type="datetime-local" class="form-control" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', \Carbon\Carbon::now()->addDay()->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="mb-3">
                <label for="delivery_status" class="form-label">Статус Доставки:</label>
                <select class="form-control" id="delivery_status" name="delivery_status" required>
                    <option value="pending" {{ old('delivery_status') == 'pending' ? 'selected' : '' }}>В ожидании</option>
                    <option value="assigned" {{ old('delivery_status') == 'assigned' ? 'selected' : '' }}>Назначена</option>
                    <option value="in_transit" {{ old('delivery_status') == 'in_transit' ? 'selected' : '' }}>В пути</option>
                    <option value="delivered" {{ old('delivery_status') == 'delivered' ? 'selected' : '' }}>Доставлена</option>
                    <option value="cancelled" {{ old('delivery_status') == 'cancelled' ? 'selected' : '' }}>Отменена</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="courier_id" class="form-label">Назначить Курьера:</label>
                <select class="form-control" id="courier_id" name="courier_id">
                    <option value="">Не назначен</option>
                    @foreach ($couriers as $courier)
                        <option value="{{ $courier->id }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>
                            {{ $courier->name }} ({{ $courier->phone ?? 'нет телефона' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Запланировать Доставку</button>
        </form>
    </div>
</body>
</html>