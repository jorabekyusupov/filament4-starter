<?php

namespace Modules\Setting\DTO;

class SettingData
{
    public function __construct(
        public string $key,
        public mixed $value,
        public string $type,
        public string $group = 'general',
        public ?string $label = null,
        public ?array $options = null,
        public bool $is_locked = false,
    ) {
    }

    public static function fromModel($model): self
    {
        return new self(
            key: $model->key,
            value: $model->value,
            type: $model->type,
            group: $model->group,
            label: $model->label,
            options: $model->options,
            is_locked: $model->is_locked,
        );
    }
}
