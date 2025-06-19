<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController; 

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
Route::post('/comparison/toggle', [ComparisonController::class, 'toggleProduct'])->name('comparison.toggle');
Route::post('/comparison/clear', [ComparisonController::class, 'clearList'])->name('comparison.clear');
Route::get('/comparison', [ComparisonController::class, 'index'])->name('comparison.index');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        } elseif ($user->isCourier()) {
            return redirect()->route('courier.dashboard');
        }
        // Запасное представление, если роль не определена или не соответствует ни одной из ожидаемых
        return view('dashboard');
    })->name('dashboard');

    // Конкретные маршруты для дашбордов, защищенные Gate'ами (политиками авторизации)
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware('can:viewAdminDashboard') // Защита: только администраторы
        ->name('admin.dashboard');

    Route::get('/client/dashboard', [DashboardController::class, 'clientDashboard'])
        ->middleware('can:viewClientDashboard') // Защита: только клиенты
        ->name('client.dashboard');

    Route::get('/courier/dashboard', [DashboardController::class, 'courierDashboard'])
        ->middleware('can:viewCourierDashboard') // Защита: только курьеры
        ->name('courier.dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('couriers', CourierController::class);
    Route::resource('payments', PaymentController::class);


});