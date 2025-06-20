<?php

namespace App\Http\Controllers;

use App\Models\Order;    
use App\Models\User;     
use App\Models\Product;  
use Illuminate\Http\Request;
use Carbon\Carbon;      

class OrderController extends Controller
{
   
    public function index()
    {
        $orders = Order::with('user')->orderBy('order_date', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->get();
        $products = Product::all();

        return view('orders.create', compact('clients', 'products'));
    }

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

    public function show(Order $order)
    {
        $order->load('user', 'products', 'delivery.courier', 'payments');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {

        $clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->get();

        $products = Product::all();

        $order->load('products');

        return view('orders.edit', compact('order', 'clients', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        // 1. Валидация входящих данных.
        $request->validate([
            'user_id' => 'required|exists:users,id',           
            'order_date' => 'required|date',                    
            'status' => 'required|string|max:50',              
            'products' => 'nullable|array',                     
            'products.*.id' => 'required_with:products|exists:products,id', 
            'products.*.quantity' => 'required_with:products|integer|min:1', 
        ]);

        $order->update([
            'user_id' => $request->user_id,
            'order_date' => Carbon::parse($request->order_date),
            'status' => $request->status,
        ]);

        $syncData = [];
        $calculatedTotalPrice = 0;

        if ($request->has('products')) {
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $syncData[$product->id] = [
                        'quantity' => $item['quantity'],
                        'price_at_order' => $product->price,
                    ];
                    $calculatedTotalPrice += $product->price * $item['quantity'];
                }
            }
        }

        $order->products()->sync($syncData);

        $order->update(['total_price' => $calculatedTotalPrice]);

        return redirect()->route('orders.index')->with('success', 'Заказ успешно обновлен!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Заказ успешно удален!');
    }
}
