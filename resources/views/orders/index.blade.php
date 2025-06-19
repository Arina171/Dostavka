<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список Заказов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Список Заказов</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary mb-3">Создать новый заказ</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Заказа</th>
                    <th>Клиент</th>
                    <th>Дата Заказа</th>
                    <th>Статус</th>
                    <th>Общая Стоимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user->name }} {{ $order->user->surname }}</td>
                        <td>{{ $order->order_date->format('d.m.Y H:i') }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ number_format($order->total_price, 2) }} руб.</td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Посмотреть</a>
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот заказ?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Заказов пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>