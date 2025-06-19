<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр Платежа #{{ $payment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Платеж #{{ $payment->id }}</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>
        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning mb-3">Редактировать Платеж</a>

        <div class="card mb-3">
            <div class="card-header">
                Детали Платежа
            </div>
            <div class="card-body">
                <p><strong>ID Платежа:</strong> {{ $payment->id }}</p>
                <p><strong>Заказ ID:</strong> <a href="{{ route('orders.show', $payment->order_id) }}">#{{ $payment->order_id }}</a></p>
                <p><strong>Сумма:</strong> {{ number_format($payment->amount, 2) }} руб.</p>
                <p><strong>Тип Оплаты:</strong> {{ $payment->payment_type }}</p>
                <p><strong>Дата Платежа:</strong> {{ $payment->payment_date->format('d.m.Y H:i') }}</p>
                <p><strong>Статус Платежа:</strong> {{ $payment->payment_status }}</p>
                <p><strong>Создан:</strong> {{ $payment->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Обновлен:</strong> {{ $payment->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>