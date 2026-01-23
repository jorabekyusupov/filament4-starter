<?php

namespace Modules\Setting\Models;

use Modules\App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'label',
        'group',
        'type',
        'options',
        'attributes',
        'is_locked',
    ];

    protected $casts = [
        'options' => 'array',
        'attributes' => 'array',
        'is_locked' => 'boolean',
    ];
}
