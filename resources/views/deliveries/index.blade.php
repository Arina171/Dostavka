<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список Доставок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Список Доставок</h1>
        <a href="{{ route('deliveries.create') }}" class="btn btn-primary mb-3">Запланировать новую доставку</a>
        <a href="{{ route('orders.index') }}" class="btn btn-info mb-3">Перейти к заказам</a>
        <a href="{{ route('products.index') }}" class="btn btn-info mb-3">Перейти к товарам</a>
        <a href="{{ route('couriers.index') }}" class="btn btn-info mb-3">Перейти к курьерам</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Заказа</th>
                    <th>Адрес</th>
                    <th>Статус Доставки</th>
                    <th>Курьер</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->id }}</td>
                        <td>{{ $delivery->order_id }}</td>
                        <td>{{ $delivery->delivery_address }}</td>
                        <td>{{ $delivery->delivery_status }}</td>
                        <td>{{ $delivery->courier->name ?? 'Не назначен' }}</td>
                        <td>
                            <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn btn-info btn-sm">Посмотреть</a>
                            <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                            <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить эту доставку?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Доставок пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>