<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;    
use App\Models\Order;  
use App\Models\Courier; 

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'name',    
        'surname', 
        'email',   
        'password',
        'role_id', 
    ];

    protected $hidden = [
        'password',       
        'remember_token', 
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // Дата и время верификации email будут преобразованы в объект Carbon
        'password' => 'hashed',            // Пароль будет автоматически хеширован при сохранении
    ];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function courier()
    {
        return $this->hasOne(Courier::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'admin';
    }

    public function isClient(): bool
    {
        // Проверяем, существует ли связанная роль и совпадает ли `role_name` с 'client'.
        return $this->role && $this->role->role_name === 'client';
    }

    public function isCourier(): bool
    {
        // Проверяем, существует ли связанная роль и совпадает ли `role_name` с 'courier'.
        return $this->role && $this->role->role_name === 'courier';
    }

    public function hasRole(string $roleName): bool
    {
        // Проверяем, существует ли связанная роль и совпадает ли ее `role_name` с переданным значением.
        return $this->role && $this->role->role_name === $roleName;
    }
}