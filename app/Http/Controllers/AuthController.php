<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ComparisonList;
use App\Models\ComparisonListItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Максимальное количество товаров, которые можно добавить в список сравнения.
    const MAX_COMPARISON_ITEMS = 4;

    public function registerForm()
    {
        // Возвращаем представление для регистрации.
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Валидируем входящие данные для регистрации.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            // Кастомные сообщения об ошибках валидации.
            'name.required' => 'Имя обязательно для заполнения.',
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный формат email.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Пароль обязателен для заполнения.',
            'password.min' => 'Пароль должен быть не менее 8 символов.',
            'password.confirmed' => 'Подтверждение пароля не совпадает.',
        ]);

        // Получаем ID текущей сессии до авторизации пользователя. Это нужно для переноса списка сравнения.
        $sessionId = session()->getId(); 

        // Создаем нового пользователя в базе данных.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Хэшируем пароль перед сохранением.
        ]);

        // Автоматически авторизуем нового пользователя.
        Auth::login($user);

        // Ищем анонимный список сравнения, связанный с текущей сессией.
        $anonymousComparisonList = ComparisonList::where('session_id', $sessionId)->first();
        $skippedItemsCount = 0; // Инициализируем счетчик пропущенных товаров.

        // Если найден анонимный список сравнения.
        if ($anonymousComparisonList) {

            $userComparisonList = ComparisonList::firstOrCreate(
                ['user_id' => $user->id], 
                ['session_id' => null] 
            );

            // Перебираем все элементы из анонимного списка.
            foreach ($anonymousComparisonList->items as $item) {
                // Проверяем, не превысит ли добавление текущего элемента максимальное количество.
                if ($userComparisonList->items()->count() < self::MAX_COMPARISON_ITEMS) {

                    ComparisonListItem::firstOrCreate([
                        'comparison_list_id' => $userComparisonList->id,
                        'product_id' => $item->product_id,
                    ]);
                } else {
                    // Если лимит достигнут, увеличиваем счетчик пропущенных товаров.
                    $skippedItemsCount++;
                }
            }

            $anonymousComparisonList->items()->delete();
            $anonymousComparisonList->delete();
        }

        // Формируем сообщение для пользователя.
        $message = 'Вы успешно зарегистрированы и вошли в систему!';
        // Если были пропущенные товары, добавляем информацию об этом в сообщение.
        if ($skippedItemsCount > 0) {
            $message .= ' Однако ' . $skippedItemsCount . ' товаров не были перенесены в ваш список сравнения из-за ограничения в ' . self::MAX_COMPARISON_ITEMS . ' товаров.';
        }
        // Перенаправляем пользователя на главную страницу с сообщением об успехе.
        return redirect()->route('home')->with('success', $message);
    }


    public function loginForm()
    {
        // Возвращаем представление для входа.
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Валидируем входящие данные для входа.
        $request->validate([
            'email' => 'required|string|email', 
            'password' => 'required|string',    
        ], [
            // Кастомные сообщения об ошибках валидации.
            'email.required' => 'Email обязателен для заполнения.',
            'email.email' => 'Введите корректный формат email.',
            'password.required' => 'Пароль обязателен для заполнения.',
        ]);

        // Получаем учетные данные (email и пароль) из запроса.
        $credentials = $request->only('email', 'password');
        // Сохраняем ID текущей сессии до ее регенерации.
        $sessionId = session()->getId(); 

        // Пытаемся авторизовать пользователя, без "запоминания".
        if (!Auth::attempt($credentials, false)) {
            // Если авторизация не удалась, выбрасываем исключение валидации с сообщением.
            throw ValidationException::withMessages([
                'email' => ['Предоставленные учетные данные неверны.'],
            ]);
        }

        // Регенерируем ID сессии для повышения безопасности.
        $request->session()->regenerate();

        // Получаем авторизованного пользователя.
        $user = Auth::user();
        // Ищем список сравнения, принадлежащий авторизованному пользователю.
        $authenticatedComparisonList = ComparisonList::where('user_id', $user->id)->first();
        // Ищем анонимный список сравнения, связанный с предыдущей сессией.
        $anonymousComparisonList = ComparisonList::where('session_id', $sessionId)->first();

        $skippedItemsCount = 0; // Инициализируем счетчик пропущенных товаров.

        // Проверяем, существует ли анонимный список и отличается ли он от списка авторизованного пользователя.
        if ($anonymousComparisonList && $anonymousComparisonList->id !== optional($authenticatedComparisonList)->id) {
            // Если у авторизованного пользователя нет своего списка сравнения.
            if (!$authenticatedComparisonList) {
                // Проверяем, не превышает ли анонимный список лимит сам по себе.
                if ($anonymousComparisonList->items()->count() <= self::MAX_COMPARISON_ITEMS) {
                    // Если анонимный список не превышает лимит, просто перепривязываем его к пользователю.
                    $anonymousComparisonList->user_id = $user->id;
                    $anonymousComparisonList->session_id = null; // Отвязываем от сессии.
                    $anonymousComparisonList->save();
                } else {
                    // Если анонимный список больше лимита, создаем новый список для пользователя
                    // и переносим только первые MAX_COMPARISON_ITEMS товаров.
                    $authenticatedComparisonList = ComparisonList::firstOrCreate(
                        ['user_id' => $user->id],
                        ['session_id' => null]
                    );
                    foreach ($anonymousComparisonList->items as $item) {
                        if ($authenticatedComparisonList->items()->count() < self::MAX_COMPARISON_ITEMS) {
                            ComparisonListItem::firstOrCreate([
                                'comparison_list_id' => $authenticatedComparisonList->id,
                                'product_id' => $item->product_id,
                            ]);
                        } else {
                            $skippedItemsCount++;
                        }
                    }
                    // Удаляем анонимный список, так как его содержимое перенесено.
                    $anonymousComparisonList->items()->delete();
                    $anonymousComparisonList->delete();
                }
            } else {
                // Если у обоих (анонимного и авторизованного) есть списки, объединяем их.
                foreach ($anonymousComparisonList->items as $item) {
                    // Проверяем лимит перед добавлением каждого элемента.
                    if ($authenticatedComparisonList->items()->count() < self::MAX_COMPARISON_ITEMS) {
                        ComparisonListItem::firstOrCreate([
                            'comparison_list_id' => $authenticatedComparisonList->id,
                            'product_id' => $item->product_id,
                        ]);
                    } else {
                        // Увеличиваем счетчик пропущенных, если лимит достигнут.
                        $skippedItemsCount++; 
                    }
                }
                // Удаляем старый анонимный список после переноса его элементов.
                $anonymousComparisonList->items()->delete();
                $anonymousComparisonList->delete();
            }
        }

        // Формируем сообщение для пользователя после входа.
        $message = 'Вы успешно вошли в систему!';
        // Если были пропущенные товары, добавляем соответствующее уведомление.
        if ($skippedItemsCount > 0) {
            $message .= ' Однако ' . $skippedItemsCount . ' товаров не были перенесены в ваш список сравнения из-за ограничения в ' . self::MAX_COMPARISON_ITEMS . ' товаров.';
        }

        return redirect()->intended(route('home'))->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Перенаправляем пользователя на главную страницу с сообщением об успехе.
        return redirect()->route('home')->with('success', 'Вы успешно вышли из системы!');
    }
}