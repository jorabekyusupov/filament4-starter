<?php

declare(strict_types=1);

namespace Modules\Translate\Models;

use Modules\App\Models\BaseModel;

/**
 * @property string $key
 * @property string $group
 */
class TranslationEntry extends BaseModel
{
    protected $table = 'translations';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['id'];

    public static function fromArray(array $attributes): self
    {
        /** @var self $model */
        $model = (new self())->forceFill($attributes);

        return $model;
    }

    public function getIdAttribute(): string
    {
        return $this->buildCompositeKey();
    }

    public function getKey(): string
    {
        return $this->buildCompositeKey();
    }

    private function buildCompositeKey(): string
    {
        $group = (string) $this->getAttribute('group');
        $key = (string) $this->getAttribute('key');

        return "{$group}::{$key}";
    }
}
