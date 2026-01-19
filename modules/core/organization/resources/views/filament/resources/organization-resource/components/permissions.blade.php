@php
    $groupedPermissions = \Modules\RolePermission\Models\Permission::all()
        ->groupBy('module')
        ->sortKeys()
        ->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('group')->sortKeys();
        });

    $colorPalettes = [
        ['name' => 'indigo', 'classes' => [
            'line' => 'bg-indigo-500', 
            'btn_text' => 'text-indigo-600 hover:text-indigo-800',
            'group_hover' => 'group-hover:text-indigo-600',
            'checkbox_ring' => 'peer-focus:ring-indigo-100',
            'checkbox_checked' => 'peer-checked:bg-indigo-600',
            'text' => 'text-indigo-600'
        ]],
        ['name' => 'emerald', 'classes' => [
            'line' => 'bg-emerald-500', 
            'btn_text' => 'text-emerald-600 hover:text-emerald-800',
            'group_hover' => 'group-hover:text-emerald-600',
            'checkbox_ring' => 'peer-focus:ring-emerald-100',
            'checkbox_checked' => 'peer-checked:bg-emerald-500',
            'text' => 'text-emerald-600'
        ]],
        ['name' => 'amber', 'classes' => [
            'line' => 'bg-amber-500', 
            'btn_text' => 'text-amber-600 hover:text-amber-800',
            'group_hover' => 'group-hover:text-amber-600',
            'checkbox_ring' => 'peer-focus:ring-amber-100',
            'checkbox_checked' => 'peer-checked:bg-amber-500',
            'text' => 'text-amber-600'
        ]],
        ['name' => 'rose', 'classes' => [
            'line' => 'bg-rose-500', 
            'btn_text' => 'text-rose-600 hover:text-rose-800',
            'group_hover' => 'group-hover:text-rose-600',
            'checkbox_ring' => 'peer-focus:ring-rose-100',
            'checkbox_checked' => 'peer-checked:bg-rose-500',
            'text' => 'text-rose-600'
        ]],
        ['name' => 'blue', 'classes' => [
            'line' => 'bg-blue-500', 
            'btn_text' => 'text-blue-600 hover:text-blue-800',
            'group_hover' => 'group-hover:text-blue-600',
            'checkbox_ring' => 'peer-focus:ring-blue-100',
            'checkbox_checked' => 'peer-checked:bg-blue-600',
            'text' => 'text-blue-600'
        ]],
    ];

    $paletteCount = count($colorPalettes);
    $moduleIndex = 0;
@endphp

<div class="space-y-6">
    @foreach ($groupedPermissions as $module => $groups)
        @php
            $currentPalette = $colorPalettes[$moduleIndex % $paletteCount];
            $moduleIndex++;
            $currentModuleIndex = $loop->index; // Capture index for unique keys
        @endphp

        <div class="mb-4" x-data="{ open: true }" wire:key="module-{{ $currentModuleIndex }}">
            <div @click="open = !open" 
                 class="flex items-center justify-between cursor-pointer bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <div class="flex items-center space-x-3">
                    <div class="h-6 w-1 {{ $currentPalette['classes']['line'] }} rounded-full"></div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide">
                        {{ $module ?: __('General') }}
                    </h2>
                </div>
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="w-6 h-6 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <div x-show="open" x-collapse
                 class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                @foreach ($groups as $group => $permissions)
                    @php
                        $groupId = 'group-' . md5($module . '-' . $group);
                    @endphp
                    <div x-data="{
                        id: '{{ $groupId }}',
                        groupExpanded: false,
                        allChecked: false,
                        init() {
                            this.updateAllChecked();
                        },
                        toggleAll() {
                            this.allChecked = !this.allChecked;
                            const value = this.allChecked;
                            $el.querySelectorAll('input[type=checkbox]').forEach((checkbox) => {
                                checkbox.checked = value;
                                checkbox.dispatchEvent(new Event('change'));
                            });
                        },
                        updateAllChecked() {
                             const checkboxes = Array.from($el.querySelectorAll('input[type=checkbox]'));
                             this.allChecked = checkboxes.length > 0 && checkboxes.every(c => c.checked);
                        }, 
                        checkPermission(){
                            this.updateAllChecked();
                        }
                    }"
                    id="{{ $groupId }}"
                    style="align-self: start;"
                    wire:key="{{ $groupId }}-{{ $loop->parent->index }}-{{ $loop->index }}"
                    class="relative bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow duration-300 h-fit self-start">
                        
                        <!-- Card Header (Toggle) -->
                        <div x-on:click.stop="groupExpanded = !groupExpanded"
                             class="bg-gray-50 dark:bg-gray-800 px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center cursor-pointer">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-700 dark:text-gray-200">{{ $group ?: __('General') }}</h3>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" 
                                     :class="{'rotate-180': groupExpanded}" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            <button type="button" 
                                x-on:click.stop="toggleAll()" 
                                class="text-xs font-medium select-all-btn {{ $currentPalette['classes']['btn_text'] }}">
                                <span x-text="allChecked ? '{{ __('Barini bekor qilish') }}' : '{{ __('Barini tanlash') }}'"></span>
                            </button>
                        </div>

                        <!-- Card Body (Content) -->
                        <!-- Card Body (Content) -->
                        <div x-show="groupExpanded" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute top-full left-0 w-full z-50 mt-1 p-5 space-y-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl max-h-80 overflow-y-auto">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center justify-between group">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 {{ $currentPalette['classes']['group_hover'] }} transition">
                                            {{ $permission->translations['name'][app()->getLocale()] ?? $permission->name }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $permission->name }}</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               value="{{ $permission->id }}" 
                                               wire:model="{{ $getStatePath() }}"
                                               x-on:change="checkPermission()"
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 {{ $currentPalette['classes']['checkbox_ring'] }} rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all {{ $currentPalette['classes']['checkbox_checked'] }}"></div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
