<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'tbl_users';
    protected $primaryKey = 'user_id';
    protected $guarded = [];

    protected $casts = [
        'old_vallue' => 'array',
    ];

    protected $hidden = [
        'password',
        'old_vallue',
    ];
}