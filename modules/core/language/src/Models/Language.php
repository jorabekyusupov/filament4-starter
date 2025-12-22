<?php

namespace Modules\Language\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Language\Observer\LanguageObserver;
use Modules\Language\Policies\LanguagePolicy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use function Laravel\Prompts\select;

#[UsePolicy(LanguagePolicy::class)]
#[ObservedBy([LanguageObserver::class])]
class Language extends Model
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
        return[
            self::STATUS_ACTIVE => 'default',
            self::STATUS_INACTIVE => 'not default',
        ];

    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('language')
            ->setDescriptionForEvent(fn (string $eventName) => "Language has been {$eventName}");
    }
}

