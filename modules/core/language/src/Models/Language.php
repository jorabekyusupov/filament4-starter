<?php

namespace Modules\Language\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\App\Models\BaseModel;
use Modules\Language\Observer\LanguageObserver;
use Modules\Language\Policies\LanguagePolicy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function Laravel\Prompts\select;

#[UsePolicy(LanguagePolicy::class)]
#[ObservedBy([LanguageObserver::class])]
class Language extends BaseModel
{
    use SoftDeletes;
    protected $table = 'languages';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $fillable = [
        'name',
        'code',
        'locale',
        'flag',
        'icon',
        'status',
    ];


    public function scopeActiveLanguages()
    {
        return $this->newQuery()->where('status', 1);
    }

    public static function getStatus()
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_INACTIVE => 'inactive',
        ];

    }

    public static function getDefault()
    {
        return [
            self::STATUS_ACTIVE => 'default',
            self::STATUS_INACTIVE => 'not default',
        ];

    }


}

