<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список Курьеров</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Список Курьеров</h1>
        <a href="{{ route('couriers.create') }}" class="btn btn-primary mb-3">Добавить нового курьера</a>
        <a href="{{ route('orders.index') }}" class="btn btn-info mb-3">Перейти к заказам</a>
        <a href="{{ route('products.index') }}" class="btn btn-info mb-3">Перейти к товарам</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя Курьера</th>
                    <th>Телефон</th>
                    <th>Связанный Пользователь (Email)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($couriers as $courier)
                    <tr>
                        <td>{{ $courier->id }}</td>
                        <td>{{ $courier->name }}</td>
                        <td>{{ $courier->phone ?? 'Не указан' }}</td>
                        <td>{{ $courier->user->email ?? 'Нет связанного пользователя' }}</td>
                        <td>
                            <a href="{{ route('couriers.show', $courier->id) }}" class="btn btn-info btn-sm">Посмотреть</a>
                            <a href="{{ route('couriers.edit', $courier->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                            <form action="{{ route('couriers.destroy', $courier->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этого курьера?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Курьеров пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>