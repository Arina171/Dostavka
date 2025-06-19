<?php

namespace App\Http\Controllers;

use App\Models\Order;    // Импортируем модель Order для работы с заказами
use App\Models\User;     // Импортируем модель User (для получения клиентов)
use App\Models\Product;  // Импортируем модель Product (для товаров в заказе)
use Illuminate\Http\Request;
use Carbon\Carbon;       // Для работы с датами и временем

class OrderController extends Controller
{
    /**
     * Отображает список всех заказов.
     * (Метод index оставлен для полноты, хотя напрямую не относится к /orders/2/edit)
     */
    public function index()
    {
        $orders = Order::with('user')->orderBy('order_date', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Показывает форму для создания нового заказа.
     * (Метод create оставлен для полноты)
     */
    public function create()
    {
        $clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->get();
        $products = Product::all();

        return view('orders.create', compact('clients', 'products'));
    }

    /**
     * Сохраняет новый заказ в хранилище.
     * (Метод store оставлен для полноты)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_date' => 'required|date',
            'status' => 'required|string|max:50',
            'products' => 'nullable|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'order_date' => Carbon::parse($request->order_date),
            'status' => $request->status,
            'total_price' => 0,
        ]);

        $totalPrice = 0;
        if ($request->has('products')) {
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $order->products()->attach($product->id, [
                        'quantity' => $item['quantity'],
                        'price_at_order' => $product->price,
                    ]);
                    $totalPrice += $product->price * $item['quantity'];
                }
            }
        }
        $order->update(['total_price' => $totalPrice]);

        return redirect()->route('orders.index')->with('success', 'Заказ успешно создан!');
    }

    /**
     * Отображает информацию о конкретном заказе.
     * (Метод show оставлен для полноты)
     */
    public function show(Order $order)
    {
        $order->load('user', 'products', 'delivery.courier', 'payments');
        return view('orders.show', compact('order'));
    }

    /**
     * Показывает форму для редактирования существующего заказа.
     * Этот метод подготавливает данные для `orders/edit.blade.php`.
     *
     * @param Order $order Объект заказа, автоматически внедренный Laravel (Model Binding).
     * @return \Illuminate\View\View
     */
    public function edit(Order $order)
    {
        // 1. Получаем всех пользователей, которые имеют роль 'client'.
        // Это нужно для выпадающего списка "Пользователь" в форме редактирования заказа.
        $clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->get();

        // 2. Получаем все доступные товары.
        // Это нужно для динамического добавления товаров в заказ на фронтенде.
        $products = Product::all();

        // 3. Загружаем связанные продукты для текущего редактируемого заказа.
        // `load('products')` гарантирует, что к `$order` будут прикреплены данные о товарах
        // вместе с pivot-информацией (количество, цена на момент заказа).
        $order->load('products');

        // Возвращаем представление 'orders.edit' и передаем ему:
        // - `$order`: Объект редактируемого заказа.
        // - `$clients`: Коллекция пользователей-клиентов.
        // - `$products`: Коллекция всех товаров.
        return view('orders.edit', compact('order', 'clients', 'products'));
    }

    /**
     * Обновляет информацию о конкретном заказе в базе данных.
     * Этот метод обрабатывает данные, отправленные из формы `orders/edit.blade.php`.
     *
     * @param Request $request Входящий HTTP-запрос с данными формы.
     * @param Order $order Объект заказа, автоматически внедренный Laravel (Model Binding).
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        // 1. Валидация входящих данных.
        $request->validate([
            'user_id' => 'required|exists:users,id',            // ID пользователя обязателен и должен существовать.
            'order_date' => 'required|date',                    // Дата заказа обязательна и должна быть валидной датой.
            'status' => 'required|string|max:50',               // Статус заказа обязателен, строка, до 50 символов.
            'products' => 'nullable|array',                     // Массив товаров необязателен.
            'products.*.id' => 'required_with:products|exists:products,id', // ID каждого товара обязателен, если массив 'products' есть.
            'products.*.quantity' => 'required_with:products|integer|min:1', // Количество каждого товара обязательно, минимум 1.
        ]);

        // 2. Обновляем основные поля заказа.
        // `total_price` не обновляется здесь напрямую, так как она будет пересчитана ниже.
        $order->update([
            'user_id' => $request->user_id,
            'order_date' => Carbon::parse($request->order_date),
            'status' => $request->status,
        ]);

        // 3. Подготавливаем данные для синхронизации товаров.
        $syncData = [];
        $calculatedTotalPrice = 0;

        if ($request->has('products')) {
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    // Формируем ассоциативный массив для метода `sync()`:
                    // ключ = ID продукта, значение = массив с дополнительными данными для pivot-таблицы.
                    $syncData[$product->id] = [
                        'quantity' => $item['quantity'],
                        'price_at_order' => $product->price, // Фиксируем цену товара на момент заказа.
                    ];
                    $calculatedTotalPrice += $product->price * $item['quantity'];
                }
            }
        }

        // 4. Синхронизируем товары, связанные с заказом.
        // `sync()` автоматически добавляет новые, удаляет отсутствующие и обновляет существующие связи.
        $order->products()->sync($syncData);

        // 5. Обновляем итоговую стоимость заказа после синхронизации товаров.
        $order->update(['total_price' => $calculatedTotalPrice]);

        // 6. Перенаправляем пользователя обратно на список заказов с сообщением об успехе.
        return redirect()->route('orders.index')->with('success', 'Заказ успешно обновлен!');
    }

    /**
     * Удаляет указанный ресурс (заказ) из хранилища.
     * (Метод destroy оставлен для полноты)
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Заказ успешно удален!');
    }
}
