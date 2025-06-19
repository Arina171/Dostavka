<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр Товара: {{ $product->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Товар: {{ $product->name }}</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning mb-3">Редактировать Товар</a>

        <div class="card mb-3">
            <div class="card-header">
                Детали Товара
            </div>
            <div class="card-body">
                <p><strong>ID Товара:</strong> {{ $product->id }}</p>
                <p><strong>Название:</strong> {{ $product->name }}</p>
                <p><strong>Описание:</strong> {{ $product->description }}</p>
                <p><strong>Цена:</strong> {{ number_format($product->price, 2) }} руб.</p>
                <p><strong>Количество на складе:</strong> {{ $product->stock_quantity }}</p>
                <p><strong>Создан:</strong> {{ $product->created_at->format('d.m.Y H:i') }}</p>
                <p><strong>Обновлен:</strong> {{ $product->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>