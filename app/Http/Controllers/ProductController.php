<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;      
use App\Models\Category;     
use App\Models\Manufacturer; 
use Illuminate\Support\Str;  

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with(['category', 'manufacturer'])->orderBy('name')->get();

        // Возвращаем представление 'products.index' и передаем ему коллекцию товаров.
        return view('products.index', compact('products'));
    }


    public function create()
    {
        // Получаем все доступные категории из базы данных.
        $categories = Category::all();
        // Получаем всех доступных производителей из базы данных.
        $manufacturers = Manufacturer::all();
        
        // Возвращаем представление 'products.create' и передаем ему коллекции категорий и производителей.
        return view('products.create', compact('categories', 'manufacturers'));
    }

    public function store(Request $request)
    {
        // Валидируем входящие данные запроса.
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name', 
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',                      
            'price' => 'required|numeric|min:0.01',                  
            'stock_quantity' => 'required|integer|min:0',            
            'category_id' => 'nullable|exists:categories,id',        
            'manufacturer_id' => 'nullable|exists:manufacturers,id', 
            'image_url' => 'nullable|url|max:255',                   
        ]);

        Product::create($request->all());

        // Перенаправляем пользователя на страницу списка товаров с сообщением об успехе.
        return redirect()->route('products.index')->with('success', 'Товар успешно добавлен!');
    }

    public function show(Product $product)
    {
        // Загружаем связанные данные категории и производителя для отображения полной информации о товаре.
        $product->load('category', 'manufacturer');
        // Возвращаем представление 'products.show' и передаем ему объект товара.
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // Получаем все доступные категории для выпадающего списка.
        $categories = Category::all();
        // Получаем всех доступных производителей для выпадающего списка.
        $manufacturers = Manufacturer::all();
        
        // Возвращаем представление 'products.edit' и передаем ему объект товара,
        // а также коллекции категорий и производителей.
        return view('products.edit', compact('product', 'categories', 'manufacturers'));
    }

    public function update(Request $request, Product $product)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'image_url' => 'nullable|url|max:255',
        ]);

        // Обновляем запись товара в базе данных, используя все валидированные данные из запроса.
        $product->update($request->all());

        // Перенаправляем пользователя на страницу списка товаров с сообщением об успехе.
        return redirect()->route('products.index')->with('success', 'Товар успешно обновлен!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        // Перенаправляем пользователя на страницу списка товаров с сообщением об успехе.
        return redirect()->route('products.index')->with('success', 'Товар успешно удален!');
    }
}