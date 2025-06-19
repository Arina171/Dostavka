<?php

namespace App\Http\Controllers;

use App\Models\Payment; 
use App\Models\Order;   
use Illuminate\Http\Request;
use Carbon\Carbon;       

class PaymentController extends Controller
{

    public function index()
    {
        $payments = Payment::with('order.user')->orderBy('payment_date', 'desc')->get();

        // Возвращаем представление 'payments.index' и передаем ему коллекцию платежей.
        return view('payments.index', compact('payments'));
    }

    public function create()
    {

        $orders = Order::with('user')->get();

        // Возвращаем представление 'payments.create' и передаем ему коллекцию заказов.
        return view('payments.create', compact('orders'));
    }

    public function store(Request $request)
    {
        // Валидируем входящие данные запроса.
        $request->validate([
            'order_id' => 'required|exists:orders,id',  
            'amount' => 'required|numeric|min:0.01',    
            'payment_type' => 'required|string|max:50', 
            'payment_date' => 'required|date',          
            'payment_status' => 'required|string|max:50',
        ]);

        // Создаем новую запись о платеже в базе данных, используя валидированные данные.
        Payment::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'payment_date' => Carbon::parse($request->payment_date), 
            'payment_status' => $request->payment_status,
        ]);

        // Перенаправляем пользователя на страницу списка платежей с сообщением об успехе.
        return redirect()->route('payments.index')->with('success', 'Платеж успешно добавлен!');
    }

    public function show(Payment $payment)
    {
        // Загружаем связанные данные: заказ, а затем пользователя, который оформил этот заказ.
        $payment->load('order.user');

        // Возвращаем представление 'payments.show' и передаем ему объект платежа.
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {

        $orders = Order::with('user')->get();

        // Возвращаем представление 'payments.edit' и передаем ему объект платежа и коллекцию заказов.
        return view('payments.edit', compact('payment', 'orders'));
    }

    public function update(Request $request, Payment $payment)
    {
        // Валидируем входящие данные запроса. Правила валидации аналогичны тем, что используются при создании.
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'payment_status' => 'required|string|max:50',
        ]);

        // Обновляем запись о платеже в базе данных, используя валидированные данные.
        $payment->update([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'payment_date' => Carbon::parse($request->payment_date), // Преобразуем строку даты в объект Carbon.
            'payment_status' => $request->payment_status,
        ]);

        // Перенаправляем пользователя на страницу списка платежей с сообщением об успехе.
        return redirect()->route('payments.index')->with('success', 'Платеж успешно обновлен!');
    }

    public function destroy(Payment $payment)
    {
        // Удаляем запись платежа из базы данных.
        $payment->delete();

        // Перенаправляем пользователя на страницу списка платежей с сообщением об успехе.
        return redirect()->route('payments.index')->with('success', 'Платеж успешно удален!');
    }
}