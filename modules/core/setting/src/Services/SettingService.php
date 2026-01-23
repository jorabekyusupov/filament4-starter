<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Models\Setting;
use Modules\Setting\Repositories\SettingRepository;

class SettingService
{
    public function __construct(
        protected SettingRepository $repository
    ) {
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = $this->repository->findByKey($key);
            return $setting ? $this->castValue($setting->value, $setting->type) : $default;
        });
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public function set(string $key, mixed $value): Setting
    {
        $setting = $this->repository->findByKey($key);

        if (!$setting) {
            abort(404, "Setting not found: $key");
        }

        $this->repository->updateValue($setting, $value);

        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Cast value based on type.
     */
    protected function castValue(mixed $value, string $type): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'boolean', 'toggle' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'integer' => (int) $value,
            'file' => Storage::url($value),
            default => $value,
        };
    }

    /**
     * Clear all settings cache.
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
