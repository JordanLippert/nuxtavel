<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'birth_date', 'password', 'avatar', 'role', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'birth_date' => 'date',
        'password'   => 'hashed',
    ];
}
