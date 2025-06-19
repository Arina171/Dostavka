<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать Платеж #{{ $payment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Редактировать Платеж #{{ $payment->id }}</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('payments.update', $payment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="order_id" class="form-label">Заказ:</label>
                <select class="form-control" id="order_id" name="order_id" required>
                    {{-- Текущий заказ всегда доступен для выбора --}}
                    <option value="{{ $payment->order->id }}" selected>
                        Заказ #{{ $payment->order->id }} (Клиент: {{ $payment->order->user->name }} {{ $payment->order->user->surname }}, Сумма: {{ number_format($payment->order->total_price, 2) }} руб.)
                    </option>
                    @foreach ($orders as $order)
                        @if ($order->id !== $payment->order->id) {{-- Не дублируем текущий заказ --}}
                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                Заказ #{{ $order->id }} (Клиент: {{ $order->user->name }} {{ $order->user->surname }}, Сумма: {{ number_format($order->total_price, 2) }} руб.)
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Сумма:</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required min="0.01">
            </div>
            <div class="mb-3">
                <label for="payment_type" class="form-label">Тип Оплаты:</label>
                <input type="text" class="form-control" id="payment_type" name="payment_type" value="{{ old('payment_type', $payment->payment_type) }}" required>
            </div>
            <div class="mb-3">
                <label for="payment_date" class="form-label">Дата Платежа:</label>
                <input type="datetime-local" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="mb-3">
                <label for="payment_status" class="form-label">Статус Платежа:</label>
                <select class="form-control" id="payment_status" name="payment_status" required>
                    <option value="pending" {{ old('payment_status', $payment->payment_status) == 'pending' ? 'selected' : '' }}>В ожидании</option>
                    <option value="completed" {{ old('payment_status', $payment->payment_status) == 'completed' ? 'selected' : '' }}>Завершен</option>
                    <option value="failed" {{ old('payment_status', $payment->payment_status) == 'failed' ? 'selected' : '' }}>Неудачен</option>
                    <option value="refunded" {{ old('payment_status', $payment->payment_status) == 'refunded' ? 'selected' : '' }}>Возвращен</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Обновить Платеж</button>
        </form>
    </div>
</body>
</html>