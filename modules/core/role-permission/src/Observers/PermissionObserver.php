<?php

declare(strict_types=1);

namespace Modules\RolePermission\Observers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\RolePermission\Models\Permission;

class PermissionObserver
{
    public function created(Permission $permission): void
    {
        $this->updateModuleAndGroup($permission);
    }

    protected function updateModuleAndGroup(Permission $permission): void
    {
        if ($permission->module && $permission->group) {
            return;
        }

        $modelName = $this->deriveModelName($permission->name);

        if (!$modelName) {
            return;
        }

        $path = $this->findModelPath($modelName);

        if ($path) {
            $relativePath = str_replace(base_path('modules') . '/', '', $path);
            $parts = explode('/', $relativePath);

            if (count($parts) >= 2) {
                $module = $parts[0];
                $group = $parts[1];

                if ($module === 'core') {
                    $module = 'system';
                }

                $permission->module = $module;
                $permission->group = $group;
                $permission->saveQuietly();
            }
        }
    }

    protected function deriveModelName(string $permissionName): ?string
    {
        $parts = explode('_', $permissionName);
        $last = end($parts);
        $singular = Str::studly(Str::singular($last));

        // Try singular name first (e.g. 'users' -> 'User')
        if ($this->findModelPath($singular)) {
            return $singular;
        }

        // Try as is (Studly)
        $studly = Str::studly($last);
        if ($this->findModelPath($studly)) {
            return $studly;
        }

        // Try last 2 words if possible
        if (count($parts) > 1) {
            $lastTwo = array_slice($parts, -2);
            $studlyTwo = Str::studly(Str::singular(implode('_', $lastTwo)));
            if ($this->findModelPath($studlyTwo)) {
                return $studlyTwo;
            }
        }

        return null;
    }

    protected function findModelPath(string $modelName): ?string
    {
        $pattern = base_path("modules/*/*/src/Models/{$modelName}.php");
        $files = glob($pattern);

        return $files[0] ?? null;
    }
}
