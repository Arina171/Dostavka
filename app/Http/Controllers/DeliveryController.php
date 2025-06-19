<?php

namespace App\Http\Controllers;

use App\Models\Delivery; 
use App\Models\Order;    
use App\Models\Courier;  
use Illuminate\Http\Request;
use Carbon\Carbon;       
use Illuminate\Validation\Rule; 

class DeliveryController extends Controller
{

    public function index()
    {

        $deliveries = Delivery::with(['order.user', 'courier'])->orderBy('created_at', 'desc')->get();

        // Возвращаем представление 'deliveries.index' и передаем ему коллекцию доставок.
        return view('deliveries.index', compact('deliveries'));
    }


    public function create()
    {
        $ordersWithoutDelivery = Order::doesntHave('delivery')->with('user')->get();

        // Получаем всех доступных курьеров.
        $couriers = Courier::all();

        // Возвращаем представление 'deliveries.create' и передаем ему списки доступных заказов и курьеров.
        return view('deliveries.create', compact('ordersWithoutDelivery', 'couriers'));
    }


    public function store(Request $request)
    {
        // Валидируем входящие данные запроса.
        $request->validate([
            'order_id' => [
                'required',                 
                'exists:orders,id',         

                Rule::unique('deliveries', 'order_id'),
            ],
            'delivery_method' => 'required|string|max:100', 
            'delivery_address' => 'required|string|max:255', 
            'delivery_date' => 'nullable|date',             
            'delivery_status' => 'required|string|max:50',  
            'courier_id' => 'nullable|exists:couriers,id',  
        ]);

        // Создаем новую запись о доставке в базе данных.
        Delivery::create([
            'order_id' => $request->order_id,
            'delivery_method' => $request->delivery_method,
            'delivery_address' => $request->delivery_address,
            // Преобразуем строку даты в объект Carbon, если она предоставлена. Иначе оставляем null.
            'delivery_date' => $request->delivery_date ? Carbon::parse($request->delivery_date) : null,
            'delivery_status' => $request->delivery_status,
            'courier_id' => $request->courier_id,
        ]);

        // Перенаправляем пользователя на страницу списка доставок с сообщением об успехе.
        return redirect()->route('deliveries.index')->with('success', 'Доставка успешно запланирована!');
    }

    public function show(Delivery $delivery)
    {
        // Загружаем связанные данные: заказ (включая связанного пользователя) и курьера.
        $delivery->load(['order.user', 'courier']);

        // Возвращаем представление 'deliveries.show' и передаем объект доставки.
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        // Получаем всех доступных курьеров.
        $couriers = Courier::all();

        $ordersWithoutDelivery = Order::doesntHave('delivery')
            ->orWhere('id', $delivery->order_id)
            ->with('user')
            ->get();

        return view('deliveries.edit', compact('delivery', 'ordersWithoutDelivery', 'couriers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        // Валидируем входящие данные запроса для обновления.
        $request->validate([
            'order_id' => [
                'required',                 
                'exists:orders,id',         

                Rule::unique('deliveries', 'order_id')->ignore($delivery->id, 'id'),
            ],
            'delivery_method' => 'required|string|max:100', 
            'delivery_address' => 'required|string|max:255', 
            'delivery_date' => 'nullable|date',             
            'delivery_status' => 'required|string|max:50',  
            'courier_id' => 'nullable|exists:couriers,id', 
        ]);

        // Обновляем запись о доставке в базе данных.
        $delivery->update([
            'order_id' => $request->order_id,
            'delivery_method' => $request->delivery_method,
            'delivery_address' => $request->delivery_address,
            // Преобразуем строку даты в объект Carbon, если она предоставлена. Иначе оставляем null.
            'delivery_date' => $request->delivery_date ? Carbon::parse($request->delivery_date) : null,
            'delivery_status' => $request->delivery_status,
            'courier_id' => $request->courier_id,
        ]);

        // Перенаправляем пользователя на страницу списка доставок с сообщением об успехе.
        return redirect()->route('deliveries.index')->with('success', 'Доставка успешно обновлена!');
    }


    public function destroy(Delivery $delivery)
    {
        // Удаляем запись о доставке из базы данных.
        $delivery->delete();

        // Перенаправляем пользователя на страницу списка доставок с сообщением об успехе.
        return redirect()->route('deliveries.index')->with('success', 'Доставка успешно удалена!');
    }
}