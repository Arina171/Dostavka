<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComparisonList;      
use App\Models\ComparisonListItem;  
use App\Models\Product;             
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; 

class ComparisonController extends Controller
{
    const MAX_COMPARISON_ITEMS = 5;

    public function index(Request $request)
    {
        // Получаем или создаем список сравнения для текущего пользователя/сессии.
        $comparisonList = $this->getOrCreateComparisonList($request);

        $comparisonList->load('items.product.category', 'items.product.manufacturer');

        // Передаем объект списка сравнения в представление для отображения.
        return view('comparison.index', compact('comparisonList'));
    }

    public function toggleProduct(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'action' => 'required|in:add,remove',
        ]);

        // Находим товар по ID, или выбрасываем исключение 404, если не найден.
        $product = Product::findOrFail($request->product_id);
        // Получаем или создаем список сравнения.
        $comparisonList = $this->getOrCreateComparisonList($request);

        // Проверяем, существует ли уже этот товар в списке сравнения пользователя.
        $existingItem = $comparisonList->items()->where('product_id', $product->id)->first();

        // Логика добавления товара.
        if ($request->action === 'add') {
            // Если товара еще нет в списке.
            if (!$existingItem) {
                // Проверяем, не превышен ли максимальный лимит товаров.
                if ($comparisonList->items()->count() >= self::MAX_COMPARISON_ITEMS) {
                    // Если лимит достигнут, возвращаем пользователя назад с сообщением об ошибке.
                    return back()->with('error', 'Достигнут максимальный лимит товаров в списке сравнения (' . self::MAX_COMPARISON_ITEMS . ').');
                }
                // Создаем новый элемент в списке сравнения.
                $comparisonList->items()->create(['product_id' => $product->id]);
                // Возвращаем пользователя назад с сообщением об успехе.
                return back()->with('success', 'Товар "' . $product->name . '" добавлен в список сравнения.');
            } else {
                // Если товар уже есть в списке, возвращаем информацию.
                return back()->with('info', 'Товар "' . $product->name . '" уже в списке сравнения.');
            }
        } 
        // Логика удаления товара.
        elseif ($request->action === 'remove') {
            // Если товар найден в списке.
            if ($existingItem) {
                // Удаляем элемент из списка сравнения.
                $existingItem->delete();
                // Возвращаем пользователя назад с сообщением об успехе.
                return back()->with('success', 'Товар "' . $product->name . '" удален из списка сравнения.');
            } else {
                // Если товар не найден в списке, возвращаем информацию.
                return back()->with('info', 'Товар "' . $product->name . '" не найден в списке сравнения.');
            }
        }

        // В случае неизвестного действия (что не должно произойти благодаря валидации), возвращаем ошибку.
        return back()->with('error', 'Неизвестное действие.');
    }

    public function clearList(Request $request)
    {
        // Получаем список сравнения.
        $comparisonList = $this->getOrCreateComparisonList($request);
        // Удаляем все связанные элементы из списка сравнения.
        $comparisonList->items()->delete(); 

        // Возвращаем пользователя назад с сообщением об успехе.
        return back()->with('success', 'Список сравнения очищен.');
    }

    private function getOrCreateComparisonList(Request $request): ComparisonList
    {
        // Проверяем, авторизован ли пользователь.
        if (Auth::check()) {
            return ComparisonList::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null] 
            );
        } else {
            // Если пользователь анонимный, используем ID текущей сессии.
            $sessionId = Session::getId();

            return ComparisonList::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null] 
            );
        }
    }
}