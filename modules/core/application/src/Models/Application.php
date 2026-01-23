<?php

namespace Modules\Application\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Application extends Authenticatable
{

    protected $table = 'applications';
    protected $fillable = [
        'name',
        'username',
        'password',
        'webhook_url',
        'secret_private_key',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];
    protected $hidden = [
        'password',
    ];

}
