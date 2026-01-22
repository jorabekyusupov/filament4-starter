<?php

namespace Modules\RolePermission\Filament\Traits;

use Modules\RolePermission\Models\Permission;

trait ManagesPermissionSorting
{
    public function moveModule(string $moduleName, string $direction)
    {
        $modules = Permission::select('module', 'module_sort')
            ->distinct()
            ->orderBy('module_sort')
            ->get();

        $this->handleSwap($modules, 'module', $moduleName, 'module_sort', $direction, function ($item, $newSort) {
            Permission::where('module', $item->module)->update(['module_sort' => $newSort]);
        });
    }

    public function moveGroup(string $moduleName, string $groupName, string $direction)
    {
        $groups = Permission::where('module', $moduleName)
            ->select('group', 'group_sort')
            ->distinct()
            ->orderBy('group_sort')
            ->get();

        $this->handleSwap($groups, 'group', $groupName, 'group_sort', $direction, function ($item, $newSort) use ($moduleName) {
            Permission::where('module', $moduleName)
                ->where('group', $item->group)
                ->update(['group_sort' => $newSort]);
        });
    }

    public function movePermission(int $permissionId, string $direction)
    {
        $permission = Permission::find($permissionId);
        if (! $permission) {
            return;
        }

        $permissions = Permission::where('module', $permission->module)
            ->where('group', $permission->group)
            ->orderBy('sort')
            ->get();

        $this->handleSwap($permissions, 'id', $permissionId, 'sort', $direction, function ($item, $newSort) {
            $item->update(['sort' => $newSort]);
        });
    }

    protected function handleSwap($items, $keyName, $targetValue, $sortColumn, $direction, $updateCallback)
    {
        $items = $items->values(); // Reset keys
        $currentIndex = $items->search(fn ($item) => $item->{$keyName} == $targetValue);

        if ($currentIndex === false) {
            return;
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if ($targetIndex < 0 || $targetIndex >= $items->count()) {
            return;
        }

        $currentItem = $items[$currentIndex];
        $swapItem = $items[$targetIndex];

        // If sort values are the same (e.g. all 0), re-sequence everything first
        if ($currentItem->{$sortColumn} == $swapItem->{$sortColumn}) {
            foreach ($items as $index => $item) {
                // Determine new seq sort value (e.g. index * 10 or just index)
                $updateCallback($item, $index);
            }
            // Refetch or just perform the specific swap now that we have a sequence?
            // Safer to just re-run the move now that they are sequenced.
            // But to avoid recursion depth or complexity, let's just swap indexes effectively.
            
            // Re-sequence effectively assigned indices 0, 1, 2...
            // So we just need to swap the values for currentIndex and targetIndex.
            $newCurrentSort = $targetIndex;
            $newSwapSort = $currentIndex;
        } else {
            $newCurrentSort = $swapItem->{$sortColumn};
            $newSwapSort = $currentItem->{$sortColumn};
        }

        $updateCallback($currentItem, $newCurrentSort);
        $updateCallback($swapItem, $newSwapSort);
    }

    public function reorderModules(array $orderedModuleNames)
    {
        foreach ($orderedModuleNames as $index => $moduleName) {
            Permission::where('module', $moduleName)->update(['module_sort' => $index + 1]);
        }
    }

    public function reorderGroups(string $moduleName, array $orderedGroupNames)
    {
        foreach ($orderedGroupNames as $index => $groupName) {
            Permission::where('module', $moduleName)
                ->where('group', $groupName)
                ->update(['group_sort' => $index + 1]);
        }
    }

    public function reorderPermissions(array $orderedPermissionIds)
    {
        // Use a transaction or raw query for performance if needed, but loop is fine for small sets
        foreach ($orderedPermissionIds as $index => $id) {
            Permission::where('id', $id)->update(['sort' => $index + 1]);
        }
    }
}
