<?php

namespace Modules\Translate\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationEntry extends Model
{
    protected $table = 'translations';

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    public static function fromArray(array $attributes): self
    {
        /** @var self $model */
        $model = (new self())->forceFill($attributes);

        return $model;
    }
}
