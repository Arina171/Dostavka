<?php

namespace App\Http\Controllers;

use App\Models\Courier; 
use App\Models\User;    
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class CourierController extends Controller
{
   
    public function index()
    {
        $couriers = Courier::with('user')->orderBy('name')->get();

        // Возвращаем представление 'couriers.index' и передаем ему коллекцию курьеров.
        return view('couriers.index', compact('couriers'));
    }

    public function create()
    {
        $availableCourierUsers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'courier');
        })->whereDoesntHave('courier')->get();

        // Возвращаем представление 'couriers.create' и передаем список доступных пользователей.
        return view('couriers.create', compact('availableCourierUsers'));
    }

    public function store(Request $request)
    {
        // Валидируем входящие данные запроса.
        $request->validate([
            'name' => 'required|string|max:255', 
            'phone' => 'nullable|string|max:20', 
            'user_id' => [
                'nullable',                
                'exists:users,id',         
                Rule::unique('couriers', 'user_id'), 
            ],
        ]);

        // Создаем новую запись курьера, используя все валидированные данные из запроса.
        Courier::create($request->all());

        // Перенаправляем пользователя на страницу списка курьеров с сообщением об успехе.
        return redirect()->route('couriers.index')->with('success', 'Курьер успешно добавлен!');
    }


    public function show(Courier $courier)
    {
        $courier->load('user');

        // Возвращаем представление 'couriers.show' и передаем объект курьера.
        return view('couriers.show', compact('courier'));
    }

    public function edit(Courier $courier)
    {
        $availableCourierUsers = User::whereHas('role', function ($query) {
            $query->where('role_name', 'courier');
        })->orWhere('id', $courier->user_id)->get();

        return view('couriers.edit', compact('courier', 'availableCourierUsers'));
    }

    public function update(Request $request, Courier $courier)
    {
        // Валидируем входящие данные запроса для обновления.
        $request->validate([
            'name' => 'required|string|max:255', 
            'phone' => 'nullable|string|max:20', 
            'user_id' => [
                'nullable',                
                'exists:users,id',          
                
                Rule::unique('couriers', 'user_id')->ignore($courier->user_id, 'user_id'),
            ],
        ]);

        // Обновляем запись курьера, используя все валидированные данные из запроса.
        $courier->update($request->all());

        // Перенаправляем пользователя на страницу списка курьеров с сообщением об успехе.
        return redirect()->route('couriers.index')->with('success', 'Курьер успешно обновлен!');
    }

    public function destroy(Courier $courier)
    {
        // Удаляем запись курьера из базы данных.
        $courier->delete();

        // Перенаправляем пользователя на страницу списка курьеров с сообщением об успехе.
        return redirect()->route('couriers.index')->with('success', 'Курьер успешно удален!');
    }
}