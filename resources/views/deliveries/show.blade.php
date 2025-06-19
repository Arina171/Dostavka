<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр Доставки #{{ $delivery->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Доставка #{{ $delivery->id }}</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>
        <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-warning mb-3">Редактировать Доставку</a>

        <div class="card mb-3">
            <div class="card-header">
                Детали Доставки
            </div>
            <div class="card-body">
                <p><strong>ID Доставки:</strong> {{ $delivery->id }}</p>
                <p><strong>Заказ ID:</strong> <a href="{{ route('orders.show', $delivery->order_id) }}">#{{ $delivery->order_id }}</a></p>
                <p><strong>Метод Доставки:</strong> {{ $delivery->delivery_method }}</p>
                <p><strong>Адрес Доставки:</strong> {{ $delivery->delivery_address }}</p>
                <p><strong>Планируемая Дата:</strong> {{ $delivery->delivery_date?->format('d.m.Y H:i') ?? 'Не указана' }}</p>
                <p><strong>Статус Доставки:</strong> {{ $delivery->delivery_status }}</p>
                <p><strong>Курьер:</strong> {{ $delivery->courier->name ?? 'Не назначен' }}</p>
                <p><strong>Создана:</strong> {{ $delivery->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Обновлена:</strong> {{ $delivery->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>