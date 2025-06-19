<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр Курьера: {{ $courier->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Курьер: {{ $courier->name }}</h1>
        <a href="{{ route('couriers.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>
        <a href="{{ route('couriers.edit', $courier->id) }}" class="btn btn-warning mb-3">Редактировать Курьера</a>

        <div class="card mb-3">
            <div class="card-header">
                Детали Курьера
            </div>
            <div class="card-body">
                <p><strong>ID Курьера:</strong> {{ $courier->id }}</p>
                <p><strong>Имя:</strong> {{ $courier->name }}</p>
                <p><strong>Телефон:</strong> {{ $courier->phone ?? 'Не указан' }}</p>
                <p><strong>Связанный Пользователь:</strong> {{ $courier->user->name ?? 'Нет' }} {{ $courier->user->surname ?? '' }} ({{ $courier->user->email ?? '' }})</p>
                <p><strong>Создан:</strong> {{ $courier->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Обновлен:</strong> {{ $courier->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>