<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

@php
    $query = \Modules\RolePermission\Models\Permission::query()
        ->orderBy('module_sort')
        ->orderBy('group_sort')
        ->orderBy('sort');

    if (!auth()->user()->hasSuperAdmin()) {
        $userPermissionIds = auth()->user()->getAllPermissions()->pluck('id');
        $query->whereIn('id', $userPermissionIds);
    }

    $groupedPermissions = $query->get()
        ->groupBy('module')
        ->map(function ($modulePermissions) {
            return $modulePermissions->groupBy('group');
        });

    $colorPalettes = [
        [
            'name' => 'indigo',
            'classes' => [
                'line' => 'bg-indigo-500',
                'btn_text' => 'text-indigo-600 hover:text-indigo-800',
                'group_hover' => 'group-hover:text-indigo-600',
                'checkbox_ring' => 'peer-focus:ring-indigo-100',
                'checkbox_checked' => 'peer-checked:bg-indigo-600',
                'text' => 'text-indigo-600'
            ]
        ],
        [
            'name' => 'emerald',
            'classes' => [
                'line' => 'bg-emerald-500',
                'btn_text' => 'text-emerald-600 hover:text-emerald-800',
                'group_hover' => 'group-hover:text-emerald-600',
                'checkbox_ring' => 'peer-focus:ring-emerald-100',
                'checkbox_checked' => 'peer-checked:bg-emerald-500',
                'text' => 'text-emerald-600'
            ]
        ],
        [
            'name' => 'amber',
            'classes' => [
                'line' => 'bg-amber-500',
                'btn_text' => 'text-amber-600 hover:text-amber-800',
                'group_hover' => 'group-hover:text-amber-600',
                'checkbox_ring' => 'peer-focus:ring-amber-100',
                'checkbox_checked' => 'peer-checked:bg-amber-500',
                'text' => 'text-amber-600'
            ]
        ],
        [
            'name' => 'rose',
            'classes' => [
                'line' => 'bg-rose-500',
                'btn_text' => 'text-rose-600 hover:text-rose-800',
                'group_hover' => 'group-hover:text-rose-600',
                'checkbox_ring' => 'peer-focus:ring-rose-100',
                'checkbox_checked' => 'peer-checked:bg-rose-500',
                'text' => 'text-rose-600'
            ]
        ],
        [
            'name' => 'blue',
            'classes' => [
                'line' => 'bg-blue-500',
                'btn_text' => 'text-blue-600 hover:text-blue-800',
                'group_hover' => 'group-hover:text-blue-600',
                'checkbox_ring' => 'peer-focus:ring-blue-100',
                'checkbox_checked' => 'peer-checked:bg-blue-600',
                'text' => 'text-blue-600'
            ]
        ],
    ];

    $paletteCount = count($colorPalettes);
    $moduleIndex = 0;

    // Calculate total permissions for global count
    $totalPermissionsCount = $groupedPermissions->flatten(2)->count();
@endphp

<div class="space-y-6" x-data="{
        globalTotal: {{ $totalPermissionsCount }},
        globalSelected: 0,
        reorderMode: false,
        updateGlobal() {
            this.globalSelected = $el.querySelectorAll('input[type=checkbox]:checked').length;
        },
        initSortable(el, group, onUpdate) {
            return new Sortable(el, {
                group: group,
                animation: 150,
                handle: '.drag-handle', // Only drag via handle
                disabled: !this.reorderMode,
                onEnd: (evt) => {
                    const ids = Array.from(evt.to.children).map(child => child.dataset.sortId).filter(id => id);
                    onUpdate(ids);
                }
            });
        }
     }" x-init="$nextTick(() => updateGlobal())" @change.capture="updateGlobal()">

    <!-- Global Stats Header -->
    <div
        class="flex items-center justify-between bg-white dark:bg-[#2f3349] px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
        <h3 class="font-bold text-gray-700 dark:text-gray-200">{{ __('Total Permissions') }}</h3>
        <div class="flex items-center gap-3">
            <button type="button" @click="reorderMode = !reorderMode; $dispatch('toggle-sortable', reorderMode)"
                :class="reorderMode ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-[#4e5264] dark:text-gray-300'"
                class="text-sm font-medium px-3 py-1.5 rounded-lg transition-colors">
                <span x-text="reorderMode ? '{{ __('Done Sorting') }}' : '{{ __('Change Order (Swipe)') }}'"></span>
            </button>
            <span
                class="text-sm font-bold px-3 py-1 rounded-full bg-primary-50 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 border border-primary-100 dark:border-primary-800">
                <span x-text="globalSelected"></span> / <span x-text="globalTotal"></span>
            </span>
        </div>
    </div>

    <div x-init="
        const sortable = initSortable($el, 'modules', (ids) => $wire.reorderModules(ids));
        $watch('reorderMode', (value) => sortable.option('disabled', !value));
    ">
        @foreach ($groupedPermissions as $module => $groups)
            @php
                $currentPalette = $colorPalettes[$moduleIndex % $paletteCount];
                $moduleIndex++;
                $currentModuleIndex = $loop->index; // Capture index for unique keys
            @endphp

            <div class="mb-4" data-sort-id="{{ $module }}" x-data="{ 
                        open: true,
                        moduleTotal: {{ $groups->flatten()->count() }},
                        moduleSelected: 0,
                        updateModuleCount() {
                            this.moduleSelected = $el.querySelectorAll('input[type=checkbox]:checked').length;
                        }
                     }" x-init="$nextTick(() => updateModuleCount())" @change.capture="updateModuleCount()"
                wire:key="module-{{ $currentModuleIndex }}">
                <div @click="open = !open"
                    class="flex items-center justify-between cursor-pointer bg-gray-50 dark:bg-[#2f3349] p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-[#4e5264] transition relative">

                    <!-- Module Drag Handle (Overlay) -->
                    <div x-show="reorderMode"
                        class="drag-handle absolute inset-0 z-10 cursor-move bg-white/50 dark:bg-black/50 flex items-center justify-center rounded-lg border-2 border-dashed border-gray-400 dark:border-gray-500">
                        <span
                            class="bg-white dark:bg-[#2f3349] px-3 py-1 rounded shadow text-xs font-bold uppercase tracking-wider">{{ __('Drag to Reorder') }}</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="h-6 w-1 {{ $currentPalette['classes']['line'] }} rounded-full"></div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide">
                                {{ $module ?: __('General') }}
                            </h2>

                            <!-- Module Sort Controls -->


                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <span x-text="moduleSelected"></span> / <span x-text="moduleTotal"></span>
                            </span>
                        </div>
                    </div>
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="w-6 h-6 transition-transform duration-200" :class="{'rotate-180': open}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div x-show="open" x-collapse x-init="
                            const sortable = initSortable($el, 'groups-{{ $currentModuleIndex }}', (ids) => $wire.reorderGroups('{{ $module }}', ids));
                            $watch('reorderMode', (value) => sortable.option('disabled', !value));
                         " class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
                    @foreach ($groups as $group => $permissions)
                        @php
                            $groupId = 'group-' . md5($module . '-' . $group);
                        @endphp
                        <div x-data="{
                                        id: '{{ $groupId }}',
                                        groupExpanded: false,
                                        allChecked: false,
                                        selectedCount: 0,
                                        totalCount: {{ $permissions->count() }},
                                        init() {
                                            this.$nextTick(() => {
                                                this.updateStats();
                                            });
                                            $watch('groupExpanded', value => {
                                                if (value) {
                                                    $dispatch('dropdown-opened', { id: this.id });
                                                }
                                            });
                                        },
                                        toggleAll() {
                                            this.allChecked = !this.allChecked;
                                            const value = this.allChecked;
                                            $el.querySelectorAll('input[type=checkbox]').forEach((checkbox) => {
                                                checkbox.checked = value;
                                                checkbox.dispatchEvent(new Event('change'));
                                            });
                                            this.updateStats();
                                        },
                                        updateStats() {
                                             const checkboxes = Array.from($el.querySelectorAll('input[type=checkbox]'));
                                             this.selectedCount = checkboxes.filter(c => c.checked).length;
                                             this.allChecked = this.selectedCount === this.totalCount && this.totalCount > 0;
                                        }, 
                                        checkPermission(){
                                            this.updateStats();
                                        }
                                    }" data-sort-id="{{ $group }}"
                            @dropdown-opened.window="if ($event.detail.id !== id) groupExpanded = false"
                            @click.outside="groupExpanded = false" id="{{ $groupId }}" style="align-self: start;"
                            wire:key="{{ $groupId }}-{{ $loop->parent->index }}-{{ $loop->index }}"
                            :class="{ 'z-50': groupExpanded }"
                            class="relative bg-white dark:bg-[#2f3349] rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 hover:shadow-md transition-shadow duration-300 h-fit self-start">

                            <!-- Card Header (Toggle) -->
                            <div x-on:click.stop="groupExpanded = !groupExpanded"
                                class="bg-gray-50 dark:bg-[#25293c] rounded-xl px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center cursor-pointer relative">

                                <!-- Group Drag Handle -->
                                <div x-show="reorderMode"
                                    class="drag-handle absolute inset-0 z-10 cursor-move bg-white/50 dark:bg-black/50 flex items-center justify-center rounded-xl border border-dashed border-gray-300">
                                    <!-- Reusing same label style or making it icon only -->
                                    <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                </div>

                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-700 dark:text-gray-200">{{ $group ?: __('General') }}
                                    </h3>

                                    <!-- Group Sort Controls -->


                                    <span
                                        class="text-xs font-medium px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        <span x-text="selectedCount"></span>/<span x-text="totalCount"></span>
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{'rotate-180': groupExpanded}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>

                                <button type="button" x-on:click.stop="toggleAll()"
                                    class="text-xs font-medium select-all-btn {{ $currentPalette['classes']['btn_text'] }}">
                                    <span x-text="allChecked ? '{{ __('Unselect All') }}' : '{{ __('Select All') }}'"></span>
                                </button>
                            </div>

                            <!-- Card Body (Content) -->
                            <!-- Card Body (Content) -->
                            <div x-show="groupExpanded" x-init="
                                                const sortable = initSortable($el, 'permissions-{{ $groupId }}', (ids) => $wire.reorderPermissions(ids));
                                                $watch('reorderMode', (value) => sortable.option('disabled', !value));
                                             " x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-full left-0 w-full z-50 mt-1 p-5 space-y-4 bg-white dark:bg-[#2f3349] rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl max-h-80 overflow-y-auto">
                                @foreach ($permissions as $permission)
                                    <div class="flex items-center justify-between group relative"
                                        data-sort-id="{{ $permission->id }}">
                                        <!-- Permission Drag Handle -->
                                        <div x-show="reorderMode"
                                            class="drag-handle absolute inset-y-0 -left-2 w-8 z-10 cursor-move flex items-center justify-center text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 8h16M4 16h16"></path>
                                            </svg>
                                        </div>

                                        <div class="flex flex-col" :class="{'ml-6': reorderMode}">
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-200 {{ $currentPalette['classes']['group_hover'] }} transition">
                                                {{ $permission->translations['name'][app()->getLocale()] ?? $permission->name }}
                                            </span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $permission->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- Permission Sort Controls -->


                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" value="{{ $permission->id }}"
                                                    wire:model="{{ $getStatePath() }}" x-on:change="checkPermission()"
                                                    class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 dark:bg-[#4e5264] peer-focus:outline-none peer-focus:ring-4 {{ $currentPalette['classes']['checkbox_ring'] }} rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all {{ $currentPalette['classes']['checkbox_checked'] }}">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>