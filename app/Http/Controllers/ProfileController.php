<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse; 
use Illuminate\Http\Request;          
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Redirect; 
use Illuminate\View\View;            

class ProfileController extends Controller
{

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(), 
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Сохраняем изменения в профиле пользователя в базе данных.
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Получаем объект текущего аутентифицированного пользователя.
        $user = $request->user();

        // Выполняем выход пользователя из системы.
        // Это уничтожает сессию аутентификации пользователя.
        Auth::logout();

        // Удаляем запись пользователя из базы данных.
        $user->delete();

        // Аннулируем (делаем недействительной) текущую сессию пользователя.
        $request->session()->invalidate();

        // Генерируем новый токен CSRF для текущей сессии.
        // Это важно для безопасности после выхода из системы, чтобы предотвратить атаки CSRF.
        $request->session()->regenerateToken();

        // Перенаправляем пользователя на главную страницу веб-сайта после успешного удаления аккаунта.
        return Redirect::to('/');
    }
}