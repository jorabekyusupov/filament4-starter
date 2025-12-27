@extends('layouts.vuexy')
@section('content')
    <style>
        .dropdown-scroll {
            min-height: 50px;
        }
        .card-body .row.g-3 {
            min-height: 80px;
        }

        /* Modern UI Tokens */
        .modern-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95));
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 2rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .modern-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        .modern-input-wrapper {
            margin-bottom: 1rem;
        }

        .modern-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b4b4b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modern-label i {
            font-size: 1rem;
            color: #6366f1;
        }

        .modern-input-group {
            background: #ffffff;
            border: 1.5px solid #dcdde1;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
        }

        .modern-input-group:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .modern-control {
            border: none;
            padding: 0;
            font-size: 0.95rem;
            font-weight: 500;
            color: #2d3436;
            width: 100%;
            background: transparent;
        }

        .modern-control:focus {
            outline: none;
            box-shadow: none;
        }

        .modern-control::placeholder {
            color: #b2bec3;
            font-style: italic;
        }

        .modern-btn-permissions {
            background: #6366f1;
            color: white;
            border: 1px solid transparent;
            border-radius: 10px;
            height: 40px;
            padding: 0 1.25rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
            line-height: normal;
        }

        .modern-btn-permissions:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
            color: white;
        }

        .permissions-section-header {
            padding: 1rem 0;
            margin-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
    <div>
        <div class="card">
            <div class="modern-header">
                <h4 class="modern-title">
                    @if(!empty($role))
                        {{__('update_role')}}
                    @else
                        {{__('create_role')}}
                    @endif
                </h4>

                <div class="row g-4">
                    @foreach($languages as $lang)
                        @php
                            $flag = match($lang->code) {
                                'uz', 'oz' => 'fi fi-uz',
                                'ru' => 'fi fi-ru',
                                'en' => 'fi fi-us',
                                default => 'fi fi-' . $lang->code
                            };
                        @endphp
                        <div class="col-md-4">
                            <div class="modern-input-wrapper">
                                <label class="modern-label" for="name_{{$lang->code}}">
                                    <span class="{{ $flag }} rounded-1 shadow-sm" style="width: 1.2rem; height: 0.9rem;"></span>
                                    {{__('role_name_'.$lang->code)}}
                                </label>
                                <div class="modern-input-group">
                                    <input wire:model="data.name.{{$lang->code}}"
                                           type="text"
                                           name="name[{{$lang->code}}]"
                                           class="modern-control"
                                           id="name_{{$lang->code}}"
                                           placeholder="{{__('role_name_'.$lang->code)}}..."
                                           value="{{$role ? $role->name[$lang->code] ?? '' : ''}}">
                                </div>
                                <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback mt-1">
                                    <div id="name_{{$lang->code}}_error"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <form id="createRole">
                <div class="card-body pt-0">
                    <div class="permissions-section-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark fw-bold d-flex align-items-center">
                                <div class="bg-primary p-2 rounded-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="ti ti-shield-check text-white fs-4"></i>
                                </div>
                                {{__('permissions')}}
                            </h5>
                            <div class="d-flex gap-3 align-items-center">
                                <label class="btn-select-all d-inline-flex align-items-center" style="cursor: pointer; gap: 8px;">
                                    <input class="checkbox-select-all form-check-input m-0" type="checkbox" value="all"/>
                                    <span class="text-body fw-semibold" style="font-size: 0.875rem;">{{ __('select_all') }}</span>
                                </label>

                                @if(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('super-admin'))
                                    <a href="{{ route('system.permissions.index', ['lang' => app()->getLocale()]) }}" class="modern-btn-permissions">
                                        <i class="ti ti-settings"></i>
                                        {{__('permissions')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="row mb-2" style="display: none;">
                            @foreach($languages as $lang)
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="name">{{__('role_name_'.$lang->code)}}</label>
                                        <input wire:model="data.name.{{$lang->code}}" type="text"
                                               name="name[{{$lang->code}}]"
                                               class="form-control rounded-pill "
                                               id="name_{{$lang->code}}_hidden"
                                               placeholder="{{__('role_name_'.$lang->code)}}"
                                               value="{{$role ? $role->name[$lang->code] ?? '' : ''}}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row" id="permissionBody">
                            @if(!empty($permissionsData))
                                <div class="alert alert-danger d-none" id="permissions_error"></div>
                                <div class="clearfix"></div>
                                @php
                                    // Define icons for each group
                                    $groupIcons = [
                                        'silence-mode' => 'ti ti-moon',
                                        'section-omu' => 'ti ti-clipboard-list',
                                        'strategic-planning' => 'ti ti-target',
                                        'plan-military-district' => 'ti ti-map-pin',
                                        'education-planning' => 'ti ti-book',
                                        'dashboard' => 'ti ti-layout-dashboard',
                                        'references' => 'ti ti-database',
                                        'system' => 'ti ti-settings',
                                        'special-permissions' => 'ti ti-lock',
                                    ];

                                    // Define colors for each group
                                    $groupColors = [
                                        'silence-mode' => 'secondary',
                                        'section-omu' => 'info',
                                        'strategic-planning' => 'success',
                                        'plan-military-district' => 'warning',
                                        'education-planning' => 'danger',
                                        'dashboard' => 'dark',
                                        'references' => 'info',
                                        'system' => 'secondary',
                                        'special-permissions' => 'danger',
                                    ];

                                    // Use permissionsData directly as it is already sorted by the Repository logic
                                    $sortedPermissions = $permissionsData;
                                @endphp
                                @foreach($sortedPermissions as $group=>$permissionsSecond)
                                    @php
                                        $icon = $groupIcons[$group] ?? 'ti ti-folder';
                                        $color = $groupColors[$group] ?? 'secondary';
                                        $moduleCount = count($permissionsSecond);
                                        $totalPermissions = collect($permissionsSecond)->flatten(1)->count();

                                        // Dynamic Group Title from Permission Name
                                        $groupTitle = __($group);
                                        $groupViewPerm = collect($permissionsSecond)->flatten(1)->first(fn($p) => $p['key'] === $group . '.view' && !empty($p['translatable']));
                                        if ($groupViewPerm) {
                                            $groupTitle = $groupViewPerm['name'];
                                        }
                                    @endphp
                                    <div class="col-sm-12 mt-3">
                                        <div class="card border-{{$color}} shadow-sm" data-group="{{$group ?: 'default'}}">
                                             <div class="card-header bg-secondary bg-opacity-10 border-{{$color}}" style="padding: 0.75rem 1rem;">
                                                 <div class="row align-items-center">
                                                     <div class="col-sm-6">
                                                         <h5 class="mb-0 d-flex align-items-center" style="font-size: 1rem;">
                                                             <i class="{{$icon}} me-2 text-secondary" style="font-size: 1.1rem;"></i>
                                                             <span class="text-dark">{{$groupTitle}}</span>
                                                             <span class="badge bg-secondary text-white ms-2" style="font-size: 0.75rem;">{{$totalPermissions}}</span>
                                                         </h5>
                                                     </div>
                                                     <div class="col-sm-6 text-end">

                                                         <label class="btn-select-all d-inline-flex align-items-center px-3 py-2" style="cursor: pointer; gap: 8px;">
                                                             <input class="checkbox-select-all-item-group form-check-input m-0"
                                                                    type="checkbox" value="{{$group}}" style="cursor: pointer;"/>
                                                             <span class="text-body" style="font-size: 0.875rem; font-weight: 500;">{{ __('select_all') }}</span>
                                                         </label>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="card-body" style="padding-top: 1.25rem;">
                                                <div class="row g-3">
                                                    @foreach($permissionsSecond as $module=>$permission)
                                                    <div class="col-sm-3" data-module="{{$module}}">
                                                        <div class="form-group">
                                                            <div class="btn-group dropdown w-100">
                                                                @php
                                                                    $selectedCount = collect($permission)->where('active', true)->count();
                                                                    $totalCount = count($permission);

                                                                    // Dynamic Module Title from View Permission
                                                                    $moduleTitle = __($module);
                                                                    $moduleViewPerm = collect($permission)->first(fn($p) => $p['action'] === 'view' && !empty($p['translatable']));
                                                                    if ($moduleViewPerm) {
                                                                        $moduleTitle = $moduleViewPerm['name'];
                                                                    }
                                                                @endphp
                                                                <button style="box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3)"
                                                                        type="button"
                                                                        class="btn col-md w-100 my-1 dropdown-toggle btn-block d-flex justify-content-between align-items-center waves-effect waves-light permission-dropdown-btn"
                                                                        data-bs-toggle="dropdown"
                                                                        data-bs-auto-close="outside"
                                                                        title="{{$moduleTitle}}">
                                                                    <span class="text-truncate" style="flex: 1; text-align: left;">{{$moduleTitle}}</span>
                                                                    <span class="badge bg-secondary ms-2" style="min-width: 45px;">{{$selectedCount}}/{{$totalCount}}</span>
                                                                </button>
                                                                <div class="dropdown-menu w-100" role="menu">
                                                                    @if(count($permission)>1)
                                                                        <div class="selected-all dropdown-item">
                                                                            <label class="small select-all-item" tabIndex="-1">
                                                                                <input class="checkbox-select-all-item form-check-input"
                                                                                       type="checkbox"
                                                                                       value="all"/> {{ __('select_all') }}
                                                                            </label>
                                                                        </div>
                                                                        <div role="separator"
                                                                             class="dropdown-divider divider"></div>
                                                                    @endif

                                                                    @foreach($permission as $item)

                                                                        <div class="dropdown-item mini-item">
                                                                            @php
                                                                                // Try standard translation first
                                                                                $permissionName = __($item['name']);

                                                                                // If translation doesn't exist, try guard_name JSON or fallback
                                                                                if ($permissionName === $item['name']) {
                                                                                    if ($item['translatable']) {
                                                                                        // Try to decode guard_name as JSON
                                                                                        $guardJson = json_decode($item['guard_name'] ?? '{}', true);
                                                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($guardJson)) {
                                                                                            $permissionName = $guardJson[app()->getLocale()] ?? $guardJson['ru'] ?? $guardJson['en'] ?? $item['name'];
                                                                                        } else {
                                                                                            $permissionName = $item['name'];
                                                                                        }
                                                                                    } else {
                                                                                        // Fallback to action + module
                                                                                        $permissionName = __($item['action']) . ' ' . __($item['module']);
                                                                                    }
                                                                                }


                                                                                // Icons for actions
                                                                                $actionIcons = [
                                                                                    'view' => 'ti ti-eye',
                                                                                    'create' => 'ti ti-file-plus',
                                                                                    'edit' => 'ti ti-edit',
                                                                                    'delete' => 'ti ti-trash',
                                                                                    'add-role' => 'ti ti-user-plus',
                                                                                    'remove-role' => 'ti ti-user-minus',
                                                                                    'agree' => 'ti ti-check',
                                                                                    'upload' => 'ti ti-upload',
                                                                                    'download' => 'ti ti-download',
                                                                                    'check' => 'ti ti-check-square',
                                                                                    'comment' => 'ti ti-message',
                                                                                    'view-all' => 'ti ti-eye-check',
                                                                                ];

                                                                                $icon = $actionIcons[$item['action']] ?? 'ti ti-lock';

                                                                                // Colors for actions
                                                                                $actionColors = [
                                                                                    'view' => 'text-info',
                                                                                    'create' => 'text-success',
                                                                                    'edit' => 'text-warning',
                                                                                    'delete' => 'text-danger',
                                                                                    'add-role' => 'text-success',
                                                                                    'remove-role' => 'text-danger',
                                                                                    'agree' => 'text-success',
                                                                                    'upload' => 'text-primary',
                                                                                    'check' => 'text-info',
                                                                                    'comment' => 'text-secondary',
                                                                                ];

                                                                                $iconColor = $actionColors[$item['action']] ?? 'text-muted';
                                                                            @endphp
                                                                            <label class="small form-check-label w-100 d-flex align-items-center justify-content-between"
                                                                                   for="inp_per_{{$item['id']}}"
                                                                                   title="{{ $permissionName }}"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   style="cursor: pointer;">
                                                                                <span class="d-flex align-items-center flex-grow-1" style="min-width: 0;">
                                                                                    <i class="{{$icon}} {{$iconColor}} me-2" style="flex-shrink: 0;"></i>
                                                                                    <span class="text-truncate">{{ $permissionName }}</span>
                                                                                </span>
                                                                                <input
                                                                                        id="inp_per_{{$item['id']}}"
                                                                                        class="checkbox-item form-check-input inp-per {{$item['module']}} {{$item['group']}} ms-2"
                                                                                        type="checkbox"
                                                                                        name="permissions[]"
                                                                                        @if($item['active']) checked="true" @endif
                                                                                        value="{{$item['id']}}"
                                                                                        style="flex-shrink: 0;"
                                                                                />
                                                                            </label>

                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        {{__('not_found')}}
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>
                <div class="card-footer text-end">
                    @if(!empty($role))
                        <button type="button" id="updateRole" class="btn btn-success">{{__('update')}}</button>
                    @else
                        <button type="button" id="saveRole" class="btn btn-success">{{__('save')}}</button>
                    @endif
                </div>
            </form>
        </div>

    </div>

@endsection
@section('styles')
    <style>
        /* Modern Minimalist Select All Button */
        .btn-select-all {
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 40px;
            padding: 0 1.25rem;
            display: inline-flex;
            align-items: center;
            line-height: normal;
        }

        .btn-select-all:hover {
            background: rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.12);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .btn-select-all:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .btn-select-all input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 4px;
        }

        .btn-select-all input[type="checkbox"]:checked {
            background-color: currentColor;
            border-color: currentColor;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
    <script>
        // Initialize Sortable for Groups
        // Initialize Sortable for Groups
        var groupEl = document.getElementById('permissionBody');
        if(groupEl){
            // Group Reorder Enabled
            Sortable.create(groupEl, {
                animation: 150,
                handle: '.card-header', // Drag via card header
                onEnd: function (evt) {
                    updateOrder('group', evt.to, evt.item);
                }
            });
        }

        // Initialize Sortable for Modules (within each group)
        var moduleContainers = document.querySelectorAll('.card-body .row.g-3');
        moduleContainers.forEach(function (container) {
            Sortable.create(container, {
                group: 'modules', // Allow dragging between groups
                animation: 150,
                handle: '.permission-dropdown-btn', // Drag via the button
                onEnd: function (evt) {
                     // Update destination list (where item ended up)
                    updateOrder('module', evt.to, evt.item);

                    // If moved to a different container, also update the source list (to re-index)
                    if (evt.from !== evt.to) {
                        updateOrder('module', evt.from);
                    }
                }
            });
        });

        // Initialize Sortable for Permissions (within each module dropdown)
        var permissionContainers = document.querySelectorAll('.dropdown-menu');
        permissionContainers.forEach(function (container) {
            Sortable.create(container, {
                group: 'permissions', // Allow dragging between modules
                animation: 150,
                filter: '.selected-all, .divider', // Don't drag "Select All" or dividers
                preventOnFilter: false,
                onEnd: function (evt) {
                    // Update destination list
                    updateOrder('permission', evt.to, evt.item);

                    // If moved to a different container, also update the source list
                    if (evt.from !== evt.to) {
                        updateOrder('permission', evt.from);
                    }
                }
            });
        });

        function updateOrder(type, container = null, item = null) {
            var order = [];
            var parentId = null;
            var targetGroup = null;

            // Use provided container or fallback (for logic where item implies container)
            // If item is provided, item.parentElement IS the container it is currently in (evt.to).
            // If item NOT provided (source update), we MUST use the passed container.
            var targetContainer = (item && item.parentElement) ? item.parentElement : container;

            if(!targetContainer) return; // Safety check

            if (type === 'group') {
                $('#permissionBody .card').each(function (index) {
                    var groupName = $(this).attr('data-group');
                    if(groupName) {
                        order.push({
                            id: groupName, // Group name acts as ID
                            position: index + 1
                        });
                    }
                });
            } else if (type === 'module') {
                // If dropped into a new container, the container is the new parent.
                // We need to act on the container where the item ENDED UP.
                // Sortable 'onEnd' evt.target is the list the item was dragged FROM (if moved) or same list.
                // Actually evt.to is the target list. We should pass evt.to as container?
                // But simplified: We iterate the container passed to this function.
                // Wait, if I drag from A to B:
                // onEnd fires. evt.to is B. evt.from is A.
                // Ideally we update B.
                // Let's rely on the DOM state.

                // Refinements:
                // We need to identify the container properly.
                // Let's assume we are calling updateOrder with the NEW container.
                // If called from onEnd, we should check `evt.to`.
                // But here I'm passing `evt.target` which is `from`. Incorrect if moved.
                // However, SortableJS logic: simpler to just re-scan the container of the item.

                var targetContainer = item ? item.parentElement : container;

                // Find parent group ID (Group Name)
                // Hierarchy: .col-sm-3 (item) -> .row.g-3 (container) -> .card-body -> .card -> .card-header .checkbox..
                var card = $(targetContainer).closest('.card');
                parentId = card.find('.checkbox-select-all-item-group').val();

                $(targetContainer).find('.col-sm-3').each(function(index){
                        var moduleName = $(this).attr('data-module');
                         if(moduleName) {
                            order.push({
                                id: moduleName,
                                position: index + 1
                            });
                        }
                   });

            } else if (type === 'permission') {
                 var targetContainer = item ? item.parentElement : container;

                 // Find parent module ID (Module Name)
                 // Hierarchy: .mini-item (item) -> .dropdown-menu (container) -> .btn-group -> .form-group -> .col-sm-3
                 var col = $(targetContainer).closest('.col-sm-3');
                 parentId = col.attr('data-module');

                 // Also find the Grandparent (Group Name) to ensure partial rows get correct group
                 var card = col.closest('.card');
                 var targetGroup = card.attr('data-group'); // UPDATED: Use data-group for consistency

                   $(targetContainer).find('.mini-item input.checkbox-item').each(function(index){
                        var permissionId = $(this).val();
                         if(permissionId) {
                            order.push({
                                id: permissionId,
                                position: index + 1
                            });
                        }
                   });
            }

             // Send to backend
             $.ajax({
                type: 'POST',
                url: "{{ route('system.permissions.update-order') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type,
                    order: order,
                    parent_id: parentId, // Send parent ID (Module or Group)
                    target_group: targetGroup // Explicit group context for permissions
                },
                success: function (response) {
                    // Order updated successfully
                },
                error: function (xhr) {
                    console.error('Order update failed', xhr);
                }
            });
        }

        // Initialize Bootstrap tooltips
        $(document).ready(function() {

            // Initialize tooltips for permission items
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Also initialize tooltips for dropdown buttons
            $('.dropdown-toggle').each(function() {
                if ($(this).attr('title')) {
                    $(this).tooltip();
                }
            });

            // Accordion behavior for permission dropdowns - close others when one opens
            $(document).on('show.bs.dropdown', '.permission-dropdown-btn', function() {
                // Close all other open dropdowns
                $('.permission-dropdown-btn').not(this).each(function() {
                    var $dropdown = $(this).next('.dropdown-menu');
                    if ($dropdown.hasClass('show')) {
                        $(this).dropdown('hide');
                    }
                });
            });
        });

        // Global permission search functionality
        $(document).off('keyup', '#globalPermissionSearch').on('keyup', '#globalPermissionSearch', function() {
            var searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                $('.permission-label').closest('.dropdown-item').show();
                $('.card').show();
                return;
            }

            $('.permission-label').each(function() {
                var permissionName = $(this).data('permission-name');
                var $dropdownItem = $(this).closest('.dropdown-item');

                if (permissionName && permissionName.includes(searchTerm)) {
                    $dropdownItem.show();
                } else {
                    $dropdownItem.hide();
                }
            });

            // Hide cards that have no visible permissions
            $('.card').each(function() {
                var hasVisiblePermissions = $(this).find('.dropdown-item:visible').length > 0;
                if (hasVisiblePermissions) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });


        $(document).off('change', '.checkbox-select-all-item-group').on('change', '.checkbox-select-all-item-group', function () {
            var isChecked = $(this).is(':checked');
            var group = $(this).val();

            // Find only the checkboxes within this specific group's card
            var $card = $(this).closest('.card');
            var checkboxes = $card.find('.mini-item input[type=checkbox].' + group);

            if (isChecked) {
                checkboxes.prop("checked", true);
            } else {
                checkboxes.prop("checked", false);
            }

            changeColorOfCheckbox();
        });

        $(document).off('change', '.dropdown .selected-all input[type=checkbox]').on('change', '.dropdown .selected-all input[type=checkbox]', function() {
            var dropdown = $(this).closest('.dropdown');
            var checkboxes = $(dropdown).find('.mini-item input[type=checkbox]');

            if ($(this).is(':checked')) {
                $.each(checkboxes, function (id, checkbox) {
                    $(checkbox).prop("checked", true);
                });
            } else {
                $.each(checkboxes, function (id, checkbox) {
                    $(checkbox).prop("checked", false);
                });
            }

            changeColorOfCheckbox();
        });
        $(document).off('change', '.checkbox-select-all').on('change', '.checkbox-select-all', function () {

            var checkboxes = $('#permissionBody .dropdown-menu').find('input[type=checkbox]');
            var checkbox_length = checkboxes.length;
            var count = 0;

            $.each(checkboxes, function (id, checkbox) {
                if ($(checkbox).is(':checked')) {
                    count++;
                } else {

                }
            });
            if (checkbox_length == count) {
                $.each(checkboxes, function (id, checkbox) {

                    $(checkbox).prop("checked", false);
                });
            } else {
                $.each(checkboxes, function (id, checkbox) {

                    $(checkbox).prop("checked", true);
                });
            }

            changeColorOfCheckbox();
        });

        $(document).off('change', '#permissionBody .mini-item input[type=checkbox]').on('change', '#permissionBody .mini-item input[type=checkbox]', function () {

            changeColorOfCheckbox();
        });


        $(document).off('click', '.dropdown-menu label').on('click', '.dropdown-menu label', function (event) {

            let inp = $(event.currentTarget).find('input');

            if ($(this).hasClass('select-all-item')) {
                var child_checkboxes = $(this).parents('.dropdown-menu').find('.mini-item input[type=checkbox]');

                var checkbox_length = child_checkboxes.length;
                var count = 0;

                $.each(child_checkboxes, function (id, checkbox) {
                    if ($(checkbox).is(':checked')) {
                        count++;
                    } else {

                    }
                });

                if (checkbox_length == count) {
                    $.each(child_checkboxes, function (id, checkbox) {

                        $(checkbox).prop("checked", false);
                        $(checkbox).trigger('change');

                    });
                } else {
                    $.each(child_checkboxes, function (id, checkbox) {

                        $(checkbox).prop("checked", true);


                        $(checkbox).trigger('change');
                    });
                }

                setTimeout(function () {
                    $(inp).trigger('change');
                });
            } else {

                setTimeout(function () {
                    inp.prop('checked', !inp.prop('checked'));
                    $(inp).trigger('change');
                });
            }


            $(event.target).blur();
            return false;
        });

        function changeColorOfCheckbox() {
            var dropdowns = $('#permissionBody .dropdown');
            var dropdowns_checkbox = $(dropdowns).find('.mini-item input[type=checkbox]');
            var all_checkbox_length = dropdowns_checkbox.length;
            var all_checkbox_count = 0;

            $.each(dropdowns, function (id, elem) {

                var checkboxes = $(elem).find('.mini-item input[type=checkbox]');
                var checkbox_length = checkboxes.length;
                var count = 0;

                $.each(checkboxes, function (id, checkbox) {
                    if ($(checkbox).is(':checked')) {

                        count++;
                    }
                });

                all_checkbox_count += count;

                var class_list = 'btn-default btn-warning btn-danger btn-info btn-primary btn-success';
                $(elem).find('button').removeClass(class_list);

                // Update badge count
                var $button = $(elem).find('button');
                var $badge = $button.find('.badge');
                if ($badge.length) {
                    $badge.text(count + '/' + checkbox_length);
                }

                if (checkbox_length == count && count > 0) {
                    $(elem).find('button').addClass('btn-success');
                    $(elem).find('.selected-all input[type=checkbox]').prop("checked", true);

                } else if (count > 0 && count <= checkbox_length / 2) {
                    $(elem).find('button').addClass('btn-warning');
                    $(elem).find('.selected-all input[type=checkbox]').prop("checked", false);
                } else if (count > 0) {
                    $(elem).find('button').addClass('btn-info');
                    $(elem).find('.selected-all input[type=checkbox]').prop("checked", false);
                } else {
                    $(elem).find('button').addClass('btn-default');
                    $(elem).find('.selected-all input[type=checkbox]').prop("checked", false);
                }
            });

            // Update each group's select-all checkbox independently
            $('.card').each(function() {
                var $card = $(this);
                var groupCheckbox = $card.find('.checkbox-select-all-item-group');

                if (groupCheckbox.length) {
                    var groupValue = groupCheckbox.val();
                    var groupPermissions = $card.find('.mini-item input[type=checkbox].' + groupValue);
                    var totalGroupPerms = groupPermissions.length;
                    var checkedGroupPerms = groupPermissions.filter(':checked').length;

                    // Set group checkbox based on its own permissions only
                    if (totalGroupPerms > 0 && checkedGroupPerms === totalGroupPerms) {
                        groupCheckbox.prop("checked", true);
                    } else {
                        groupCheckbox.prop("checked", false);
                    }
                }
            });

            if (all_checkbox_length == all_checkbox_count && all_checkbox_count > 0) {
                $('.checkbox-select-all').prop("checked", true);
            } else {
                $('.checkbox-select-all').prop("checked", false);
            }
        }

        $(document).ready(function () {
            changeColorOfCheckbox();
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $('#saveRole').click(function (e) {
            var form = $('#createRole').serializeArray();
            var csrf = '{{ csrf_token() }}';
            form.push({name: '_token', value: csrf});
            $.ajax({
                type: 'POST',
                url: "{{ route('system.roles.store') }}",
                data: form,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.href = "{{ route('system.roles.index',['lang'=>app()->getLocale()]) }}";
                    }
                },
                error: function (response) {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        // . replace with _ in key
                        var newKey = key.replace(/\./g, '_');
                        console.log(newKey);
                        $('#' + newKey + '_error').html(value[0]);
                        $('#' + newKey).addClass('is-invalid');
                        $('#' + newKey + '_error').removeClass('d-none');


                    });
                }
            });
        });
        @if(!empty($role))
        $('#updateRole').click(function (e) {
            var form = $('#createRole').serializeArray();
            var csrf = '{{ csrf_token() }}';
            form.push({name: '_token', value: csrf});
            $.ajax({
                type: 'POST',
                url: "{{ route('system.roles.update',['id'=>$role->id] ) }}",
                data: form,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.href = "{{ route('system.roles.index',['lang'=>app()->getLocale()]) }}";
                    }
                },
                error: function (response) {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        // . replace with _ in key
                        var newKey = key.replace(/\./g, '_');
                        console.log(newKey);
                        $('#' + newKey + '_error').html(value[0]);
                        $('#' + newKey).addClass('is-invalid');
                        $('#' + newKey + '_error').removeClass('d-none');


                    });
                }
            });
        });
        @endif



    </script>
@endsection
