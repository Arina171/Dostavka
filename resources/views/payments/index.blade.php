<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список Платежей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Список Платежей</h1>
        <a href="{{ route('payments.create') }}" class="btn btn-primary mb-3">Добавить новый платеж</a>
        <a href="{{ route('orders.index') }}" class="btn btn-info mb-3">Перейти к заказам</a>
        <a href="{{ route('products.index') }}" class="btn btn-info mb-3">Перейти к товарам</a>
        <a href="{{ route('couriers.index') }}" class="btn btn-info mb-3">Перейти к курьерам</a>
        <a href="{{ route('deliveries.index') }}" class="btn btn-info mb-3">Перейти к доставкам</a>

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
                    <th>Сумма</th>
                    <th>Тип Оплаты</th>
                    <th>Статус Платежа</th>
                    <th>Дата Платежа</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->order_id }}</td>
                        <td>{{ number_format($payment->amount, 2) }} руб.</td>
                        <td>{{ $payment->payment_type }}</td>
                        <td>{{ $payment->payment_status }}</td>
                        <td>{{ $payment->payment_date->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-sm">Посмотреть</a>
                            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning btn-sm">Редактировать</a>
                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены, что хотите удалить этот платеж?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Платежей пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>