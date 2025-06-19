<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать Курьера: {{ $courier->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Редактировать Курьера: {{ $courier->name }}</h1>
        <a href="{{ route('couriers.index') }}" class="btn btn-secondary mb-3">Вернуться к списку</a>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('couriers.update', $courier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Имя Курьера:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $courier->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $courier->phone) }}">
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Связать с Пользователем (необязательно, только с ролью 'courier'):</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option value="">Не связывать</option>
                    @foreach ($courierUsers as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $courier->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} {{ $user->surname }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Обновить Курьера</button>
        </form>
    </div>
</body>
</html>