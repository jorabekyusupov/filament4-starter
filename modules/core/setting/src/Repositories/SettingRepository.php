<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository
{
    public function findByKey(string $key): ?Setting
    {
        return Setting::where('key', $key)->first();
    }

    public function updateValue(Setting $setting, mixed $value): bool
    {
        return $setting->update(['value' => $value]);
    }

    public function getAll(): Collection
    {
        return Setting::all();
    }
}
