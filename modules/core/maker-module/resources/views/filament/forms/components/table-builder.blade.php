<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <!-- Load FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* --- GLOBAL & VARIABLES --- */
        .table-builder-wrapper {
            --bg-body: #f8fafc;
            --border-color: #e2e8f0;
            --primary: #d97706; /* Filament Amber */
            --radius: 0.5rem;
            --text-main: #334155;
            --text-muted: #64748b;
            --bg-sidebar: #ffffff;
            --bg-item: #ffffff;
            
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: var(--bg-body);
            height: 850px;
            display: flex;
            overflow: hidden;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
        }

        /* DARK MODE */
        :is(.dark .table-builder-wrapper) {
            --bg-body: #25293c; /* theme.css --dark-bg-color */
            --border-color: #4b5563;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --bg-sidebar: #2f3349; /* theme.css --dark-card-bg-color */
            --bg-item: #2f3349;
        }

        /* --- SIDEBAR --- */
        .tb-sidebar {
            width: 300px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 15px;
            gap: 15px;
            z-index: 20;
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
            flex-shrink: 0;
            overflow-y: auto;
        }

        .tb-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .tb-draggable-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--bg-item);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: grab;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.2s;
        }
        .tb-draggable-item:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(217, 119, 6, 0.1); 
        }

        /* --- CANVAS --- */
        .tb-canvas {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .tb-drop-zone {
            min-height: 100px;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 15px;
            transition: all 0.2s;
            background: rgba(125,125,125,0.05);
        }

        .tb-drop-zone-title {
            font-size: 12px;
            font-weight: bold;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .drag-over { background-color: rgba(34, 197, 94, 0.1) !important; border-color: #22c55e !important; }

        /* --- ITEMS --- */
        .tb-comp-wrapper {
            position: relative;
            margin-bottom: 10px;
            background: var(--bg-item);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .delete-btn {
            margin-left: auto;
            color: #ef4444;
            cursor: pointer;
            opacity: 0.7;
        }
        .delete-btn:hover { opacity: 1; }
        
        .item-icon { width: 20px; text-align: center; color: var(--text-muted); }
        .item-label { font-size: 13px; font-weight: 600; }
        .item-type { font-size: 10px; color: var(--text-muted); margin-left: 5px; }

    </style>

    <div 
        x-data="tableBuilder({
            state: $wire.entangle('{{ $getStatePath() }}'),
            columns: {{ json_encode($columns) }}
        })"
        class="table-builder-wrapper"
    >
        <!-- SIDEBAR -->
        <div class="tb-sidebar">
            <h3 style="margin:0 0 20px 0; color:var(--text-main); font-weight:bold; font-size:18px;">Table Builder</h3>

            <!-- Standard Columns -->
            <div>
                <div class="tb-section-title">Column Types</div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'column_type', {type: 'TextColumn', icon: 'fas fa-font'})">
                    <i class="fas fa-font"></i> Text Column
                </div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'column_type', {type: 'IconColumn', icon: 'fas fa-icons'})">
                    <i class="fas fa-icons"></i> Icon Column
                </div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'column_type', {type: 'ImageColumn', icon: 'fas fa-image'})">
                    <i class="fas fa-image"></i> Image Column
                </div>
                 <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'column_type', {type: 'BadgeColumn', icon: 'fas fa-tag'})">
                    <i class="fas fa-tag"></i> Badge Column
                </div>
                 <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'column_type', {type: 'BooleanColumn', icon: 'fas fa-check-square'})">
                    <i class="fas fa-check-square"></i> Boolean Column
                </div>
            </div>

            <!-- Pre-defined Fields -->
            <div>
                <div class="tb-section-title">Available Fields</div>
                <template x-for="col in columns" :key="col.name">
                    <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'field', col)">
                        <i class="fas fa-database"></i>
                        <span x-text="col.name"></span>
                        <span style="margin-left:auto; font-size:10px; opacity:0.7;" x-text="col.type"></span>
                    </div>
                </template>
                <div x-show="!columns || columns.length === 0" style="font-size:12px; color:var(--text-muted); font-style:italic; padding:10px; text-align:center;">
                    (No fields found. Select a table first)
                </div>
            </div>

             <!-- Actions -->
            <div style="margin-top: 15px;">
                <div class="tb-section-title">Actions</div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'action', {type: 'EditAction', icon: 'fas fa-pen'})">
                    <i class="fas fa-pen"></i> Edit Action
                </div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'action', {type: 'DeleteAction', icon: 'fas fa-trash'})">
                    <i class="fas fa-trash"></i> Delete Action
                </div>
                 <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'action', {type: 'ViewAction', icon: 'fas fa-eye'})">
                    <i class="fas fa-eye"></i> View Action
                </div>
            </div>
            
            <!-- Filters -->
            <div style="margin-top: 15px;">
                <div class="tb-section-title">Filters</div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'filter', {type: 'SelectFilter', icon: 'fas fa-filter'})">
                    <i class="fas fa-filter"></i> Select Filter
                </div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'filter', {type: 'TernaryFilter', icon: 'fas fa-filter'})">
                    <i class="fas fa-filter"></i> Ternary Filter
                </div>
                <div class="tb-draggable-item" draggable="true" @dragstart="dragStart($event, 'filter', {type: 'Filter', icon: 'fas fa-filter'})">
                    <i class="fas fa-filter"></i> Custom Filter
                </div>
            </div>
        </div>

        <!-- CANVAS -->
        <div class="tb-canvas">
            
            <!-- COLUMNS ZONE -->
            <div class="tb-drop-zone-container">
                <div class="tb-drop-zone-title">Table Columns</div>
                <div class="tb-drop-zone"
                     :class="{ 'drag-over': draggingOver === 'columns' }"
                     @dragover.prevent="draggingOver = 'columns'"
                     @dragleave.prevent="draggingOver = null"
                     @drop.prevent="handleDrop($event, 'columns')"
                >
                     <div x-show="!state.columns || state.columns.length === 0" style="text-align: center; color: var(--text-muted); padding: 20px;">
                        Drop columns here
                    </div>
                    <template x-for="(item, index) in state.columns" :key="index">
                        <div class="tb-comp-wrapper">
                            <div class="item-icon"><i :class="item.icon || 'fas fa-columns'"></i></div>
                            <div>
                                <div class="item-label" x-text="item.label || item.name"></div>
                                <div class="item-type" x-text="item.type"></div>
                                
                                <!-- Extended Configuration -->
                                <div style="margin-top: 5px; font-size: 11px; display: flex; flex-direction: column; gap: 3px;">
                                    <!-- Translatable (For json/text OR foreignId relations) -->
                                    <template x-if="['json', 'jsonb', 'text', 'longText'].includes(item.dbType) || item.dbType === 'foreignId' || item.name.endsWith('_id')">
                                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer;">
                                            <input type="checkbox" x-model="item.is_translatable"> <span x-text="(item.dbType === 'foreignId' || item.name.endsWith('_id')) ? 'Rel. Translatable' : 'Translatable'"></span>
                                        </label>
                                    </template>
                                    
                                    <!-- Related Column (For manual foreign key mapping) -->
                                     <template x-if="item.dbType === 'foreignId' || item.name.endsWith('_id')">
                                        <div style="display:flex; align-items:center; gap:4px;">
                                           <span style="opacity:0.7">Rel. Col:</span>
                                           <input type="text" x-model="item.related_column" placeholder="name" style="border:1px solid var(--border-color); border-radius:3px; padding:1px 4px; font-size:10px; width: 60px;">
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div style="margin-left: auto; display: flex; gap: 5px;">
                                <input type="checkbox" x-model="item.sortable" title="Sortable"> <span style="font-size:10px; color:var(--text-muted)">Sort</span>
                                <input type="checkbox" x-model="item.searchable" title="Searchable"> <span style="font-size:10px; color:var(--text-muted)">Search</span>
                            </div>
                            <div class="delete-btn" @click="state.columns.splice(index, 1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- FILTERS ZONE -->
            <div class="tb-drop-zone-container">
                <div class="tb-drop-zone-title">Filters</div>
                <div class="tb-drop-zone"
                     style="min-height: 60px;"
                     :class="{ 'drag-over': draggingOver === 'filters' }"
                     @dragover.prevent="draggingOver = 'filters'"
                     @dragleave.prevent="draggingOver = null"
                     @drop.prevent="handleDrop($event, 'filters')"
                >
                    <div x-show="!state.filters || state.filters.length === 0" style="text-align: center; color: var(--text-muted); padding: 10px;">
                        Drop filters here
                    </div>
                     <template x-for="(item, index) in state.filters" :key="index">
                        <div class="tb-comp-wrapper" style="flex-wrap: wrap;">
                             <div style="display: flex; align-items: center; gap: 10px; width: 100%;">
                                 <div class="item-icon"><i :class="item.icon || 'fas fa-filter'"></i></div>
                                <div>
                                    <div class="item-label" x-text="item.label || item.name"></div>
                                    <div class="item-type" x-text="item.type"></div>
                                </div>
                                <div class="delete-btn" @click="state.filters.splice(index, 1)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                </div>
                             </div>
                             
                             <!-- Filter Configuration -->
                             <div style="width: 100%; margin-top: 8px; border-top: 1px solid var(--border-color); padding-top: 8px;">
                                 <label style="font-size: 11px; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 4px;">Target Column</label>
                                 <select x-model="item.column" style="width: 100%; font-size: 12px; padding: 4px; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-body); color: var(--text-main);">
                                     <option value="">Select Column...</option>
                                     <template x-for="col in columns" :key="col.name">
                                         <option :value="col.name" x-text="col.name"></option>
                                     </template>
                                 </select>
                             </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ACTIONS ZONE -->
            <div class="tb-drop-zone-container">
                <div class="tb-drop-zone-title">Row Actions</div>
                <div class="tb-drop-zone"
                     style="min-height: 60px;"
                     :class="{ 'drag-over': draggingOver === 'actions' }"
                     @dragover.prevent="draggingOver = 'actions'"
                     @dragleave.prevent="draggingOver = null"
                     @drop.prevent="handleDrop($event, 'actions')"
                >
                     <div x-show="!state.actions || state.actions.length === 0" style="text-align: center; color: var(--text-muted); padding: 10px;">
                        Drop actions here
                    </div>
                     <template x-for="(item, index) in state.actions" :key="index">
                        <div class="tb-comp-wrapper">
                             <div class="item-icon"><i :class="item.icon || 'fas fa-bolt'"></i></div>
                            <div>
                                <div class="item-label" x-text="item.type"></div>
                            </div>
                             <div class="delete-btn" @click="state.actions.splice(index, 1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
             <!-- HEADER ACTIONS ZONE -->
            <div class="tb-drop-zone-container">
                <div class="tb-drop-zone-title">Header Actions</div>
                <div class="tb-drop-zone"
                     style="min-height: 60px;"
                     :class="{ 'drag-over': draggingOver === 'header_actions' }"
                     @dragover.prevent="draggingOver = 'header_actions'"
                     @dragleave.prevent="draggingOver = null"
                     @drop.prevent="handleDrop($event, 'header_actions')"
                >
                     <div x-show="!state.header_actions || state.header_actions.length === 0" style="text-align: center; color: var(--text-muted); padding: 10px;">
                        Drop header actions here (e.g. Create)
                    </div>
                     <template x-for="(item, index) in state.header_actions" :key="index">
                        <div class="tb-comp-wrapper">
                             <div class="item-icon"><i :class="item.icon || 'fas fa-bolt'"></i></div>
                            <div>
                                <div class="item-label" x-text="item.type"></div>
                            </div>
                             <div class="delete-btn" @click="state.header_actions.splice(index, 1)"><i class="fas fa-times"></i></div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            function registerConfig() {
                if (typeof Alpine === 'undefined') {
                    document.addEventListener('alpine:init', registerConfig);
                    return;
                }

                if (Alpine.data['tableBuilder']) return;

                Alpine.data('tableBuilder', ({ state, columns }) => ({
                    state: state,
                    columns: columns,
                    draggedType: null,
                    draggedData: null,
                    draggingOver: null, // 'columns', 'filters', 'actions', 'header_actions'

                    init() {
                         if (!this.state || typeof this.state !== 'object') {
                             this.state = {
                                 columns: [],
                                 filters: [],
                                 actions: [],
                                 header_actions: []
                             };
                         }
                         // Ensure structure
                         if(!this.state.columns) this.state.columns = [];
                         if(!this.state.filters) this.state.filters = [];
                         if(!this.state.actions) this.state.actions = [];
                         if(!this.state.header_actions) this.state.header_actions = [];
                    },

                    dragStart(e, type, data = null) {
                        this.draggedType = type;
                        this.draggedData = data;
                        e.dataTransfer.effectAllowed = 'copy';
                        e.dataTransfer.dropEffect = 'copy';
                    },

                    handleDrop(e, zone) {
                        this.draggingOver = null;

                        if (!this.draggedType) return;

                        let item = null;

                        if (this.draggedType === 'field' && zone === 'columns') {
                            // Convert DB column to Table Column
                            item = {
                                name: this.draggedData.name,
                                label: this.draggedData.name.charAt(0).toUpperCase() + this.draggedData.name.slice(1),
                                type: 'TextColumn',
                                dbType: this.draggedData.type, // Store original DB type
                                icon: 'fas fa-font',
                                sortable: true,
                                searchable: true,
                                is_translatable: false,
                                related_column: 'name' // Default for relations
                            };
                        } else if (this.draggedType === 'column_type' && zone === 'columns') {
                            item = {
                                name: 'new_column',
                                label: 'New Column',
                                type: this.draggedData.type,
                                icon: this.draggedData.icon,
                                sortable: true,
                                searchable: true
                            };
                        } else if (this.draggedType === 'action' && (zone === 'actions' || zone === 'header_actions')) {
                             item = {
                                type: this.draggedData.type,
                                icon: this.draggedData.icon
                            };
                        } else if (this.draggedType === 'filter' && zone === 'filters') {
                             item = {
                                name: 'filter',
                                label: 'Filter',
                                type: this.draggedData.type,
                                icon: this.draggedData.icon
                            };
                        }

                        if (item) {
                            if (!this.state[zone]) this.state[zone] = [];
                            this.state[zone].push(item);
                        }
                        
                        // Reset
                        this.draggedType = null;
                        this.draggedData = null;
                    }
                }));
            }

            registerConfig();
        })();
    </script>
</x-dynamic-component>
