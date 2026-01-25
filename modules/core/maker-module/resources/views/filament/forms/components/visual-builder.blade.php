<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <!-- Load FontAwesome for 1:1 match -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* --- GLOBAL & VARIABLES --- */
        .visual-builder-wrapper {
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
            height: 700px;
            display: flex;
            overflow: hidden;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
        }

        /* DARK MODE */
        :is(.dark .visual-builder-wrapper) {
            --bg-body: #25293c; /* theme.css --dark-bg-color */
            --border-color: #4b5563;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --bg-sidebar: #2f3349; /* theme.css --dark-card-bg-color */
            --bg-item: #2f3349;
        }

        /* --- SIDEBAR --- */
        .vb-sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 15px;
            gap: 15px;
            z-index: 20;
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        .vb-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .vb-draggable-item {
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
        .vb-draggable-item:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(217, 119, 6, 0.1); 
        }

        /* --- CANVAS --- */
        .vb-canvas {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }

        .vb-drop-zone {
            /*min-height: 100px;*/ /* Dynamic */
        }

        #vb-root-drop-zone {
            max-width: 1000px;
            margin: 0 auto;
            min-height: 600px;
            padding-bottom: 150px;
        }

        /* --- COMPONENTS STYLE --- */
        .comp-wrapper {
            position: relative;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        /* Delete Button */
        .delete-btn {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 26px;
            height: 26px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            border: 2px solid white;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .comp-wrapper:hover > .delete-btn { display: flex; }

        /* FIELDSET */
        .f-fieldset {
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 20px;
            background: var(--bg-item);
        }
        .f-fieldset legend {
            font-weight: 600;
            color: var(--text-main);
            padding: 0 8px;
            font-size: 14px;
        }

        /* SECTION */
        .f-section {
            background: var(--bg-item);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* INPUTS */
        .f-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-main); }
        .f-input { width: 100%; padding: 8px 10px; border: 1px solid var(--border-color); border-radius: 6px; outline: none; box-sizing: border-box; background: transparent; color: var(--text-main);}
        .f-input:focus { border-color: var(--primary); ring: 2px solid var(--primary); }

        /* --- ADVANCED GRID SYSTEM --- */
        .f-grid-container {
            border: 1px dashed var(--border-color);
            padding: 15px;
            border-radius: 8px;
            background: rgba(125,125,125,0.05);
        }

        .grid-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            background: var(--bg-item);
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
        }

        .grid-group {
            display: flex;
            align-items: center;
            gap: 4px;
            padding-right: 15px;
            border-right: 1px solid var(--border-color);
        }
        .grid-group:last-child { border-right: none; }

        .grid-group-label {
            font-size: 10px;
            font-weight: bold;
            color: var(--text-muted);
            writing-mode: vertical-lr;
            transform: rotate(180deg);
        }

        .grid-btn {
            border: 1px solid var(--border-color);
            background: var(--bg-item);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.1s;
            color: var(--text-muted);
        }
        .grid-btn:hover { border-color: var(--primary); color: var(--primary); }
        .grid-btn.active { background: var(--primary); color: white; border-color: var(--primary); }

        .f-grid-row {
            display: grid;
            gap: 20px;
            min-height: 60px;
        }

        .f-grid-col {
            border: 1px dotted var(--border-color);
            border-radius: 5px;
            padding: 10px;
            background: rgba(125,125,125,0.05);
            min-height: 50px;
        }

        /* Drag Over Visuals */
        .drag-over { background-color: rgba(34, 197, 94, 0.1) !important; border: 2px dashed #22c55e !important; }

        /* TABS Custom (Extra) */
        .f-tabs { border: 1px solid var(--border-color); border-radius: 6px; overflow: hidden; background: var(--bg-item); }
        .f-tabs-header { display: flex; background: rgba(125,125,125,0.05); border-bottom: 1px solid var(--border-color); }
        .f-tab-item { padding: 10px 15px; font-size: 13px; font-weight: 500; cursor: pointer; border-right: 1px solid var(--border-color); color: var(--text-muted); }
        .f-tab-item.active { background: var(--bg-item); color: var(--primary); border-bottom: 2px solid var(--primary); margin-bottom: -1px; }
        .f-tab-item-add { padding: 10px; cursor: pointer; color: var(--primary); font-weight: bold; }
        .f-tab-content { padding: 15px; min-height: 50px; }

    </style>

    <div 
        x-data="visualFormBuilder({
            state: $wire.entangle('{{ $getStatePath() }}'),
            columns: {{ json_encode($columns) }}
        })"
        class="visual-builder-wrapper"
    >
        <!-- SIDEBAR -->
        <div class="vb-sidebar">
            <h3 style="margin:0 0 20px 0; color:var(--text-main); font-weight:bold; font-size:18px;">Form Builder</h3>

            <div>
                <div class="vb-section-title">Layouts</div>
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'grid')">
                    <i class="fas fa-table-columns"></i> Advanced Grid
                </div>
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'section')">
                    <i class="far fa-square"></i> Section (Card)
                </div>
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'fieldset')">
                    <i class="fas fa-compress"></i> Fieldset
                </div>
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'tabs')">
                    <i class="fas fa-folder"></i> Tabs
                </div>
            </div>

            <div>
                <div class="vb-section-title">Fields</div>
                <div style="font-size: 10px; color: red; display: none;">DEBUG: {{ json_encode($columns) }}</div>
                <template x-for="col in columns" :key="col.name">
                    <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'field', col)">
                        <i class="fas fa-font"></i>
                        <span x-text="col.name"></span>
                        <span style="margin-left:auto; font-size:10px; opacity:0.7;" x-text="col.type"></span>
                    </div>
                </template>
                <div x-show="!columns || columns.length === 0" style="font-size:12px; color:var(--text-muted); font-style:italic; padding:10px; text-align:center;">
                    (No fields found. Select a table first)
                </div>
            </div>
        </div>

        <!-- CANVAS -->
        <div class="vb-canvas">
            <div id="vb-root-drop-zone" 
                 class="vb-drop-zone" 
                 :class="{ 'drag-over': draggingOverRoot }"
                 @dragover.prevent="draggingOverRoot = true"
                 @dragleave.prevent="draggingOverRoot = false"
                 @drop.prevent="handleDrop($event, state)"
            >
                <!-- Placeholder -->
                <div x-show="state.length === 0" style="text-align: center; color: var(--text-muted); margin-top: 150px;">
                    <i class="fas fa-layer-group" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>Formani yig'ish uchun elementlarni bu yerga tashlang</p>
                </div>

                <!-- Render Items -->
                <template x-for="(item, index) in state" :key="index">
                    <div class="comp-wrapper" >
                         <!-- Delete Button -->
                        <div class="delete-btn" @click="state.splice(index, 1)"><i class="fas fa-times"></i></div>

                        <!-- GRID -->
                        <template x-if="item.type === 'grid'">
                            <div class="f-grid-container">
                                <div class="grid-controls">
                                    <div class="grid-group">
                                        <span class="grid-group-label">2 COL</span>
                                        <button type="button" class="grid-btn" :class="{ 'active': item.data.columns === 2 }" @click="item.data.columns = 2">50/50</button>
                                    </div>
                                    <div class="grid-group">
                                        <span class="grid-group-label">MULTI</span>
                                        <button type="button" class="grid-btn" :class="{ 'active': item.data.columns === 3 }" @click="item.data.columns = 3">3 Cols</button>
                                        <button type="button" class="grid-btn" :class="{ 'active': item.data.columns === 4 }" @click="item.data.columns = 4">4 Cols</button>
                                    </div>
                                </div>
                                <div class="f-grid-row" :style="'grid-template-columns: repeat(' + (item.data.columns || 2) + ', 1fr)'">
                                    <template x-for="colIndex in (item.data.columns || 2)">
                                        <div class="f-grid-col"
                                             x-data="{ isOver: false }"
                                             :class="{ 'drag-over': isOver }"
                                             @dragover.prevent.stop="isOver = true"
                                             @dragleave.prevent.stop="isOver = false"
                                             @drop.prevent.stop="isOver = false; handleDrop($event, item.data, colIndex-1)"
                                        >
                                            <div x-init="if(!item.data.items) item.data.items = {}; if(!item.data.items[colIndex-1]) item.data.items[colIndex-1] = []"></div>
                                            <template x-for="(subItem, subIndex) in (item.data.items[colIndex-1] || [])" :key="subIndex">
                                                <div class="comp-wrapper" style="margin-bottom:10px;">
                                                    <div class="delete-btn" @click="item.data.items[colIndex-1].splice(subIndex, 1)"><i class="fas fa-times"></i></div>
                                                    <!-- Simple Field Preview for Grid -->
                                                    <div style="background:var(--bg-item); padding:8px; border:1px solid var(--border-color); border-radius:4px;">
                                                        <label class="f-label" x-text="subItem.data.label || subItem.data.column"></label>
                                                        <input type="text" class="f-input" disabled placeholder="...">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- SECTION -->
                        <template x-if="item.type === 'section'">
                            <div class="f-section">
                                <div style="font-weight:bold; margin-bottom:10px; border-bottom:1px solid var(--border-color); padding-bottom:5px;">
                                    <input x-model="item.data.label" style="border:none; width:100%; outline:none; background:transparent; color:var(--text-main);" placeholder="Section Title">
                                </div>
                                <div class="vb-drop-zone"
                                     style="min-height:40px;"
                                     x-data="{ isOver: false }"
                                     :class="{ 'drag-over': isOver }"
                                     @dragover.prevent.stop="isOver = true"
                                     @dragleave.prevent.stop="isOver = false"
                                     @drop.prevent.stop="isOver = false; handleDrop($event, item.data)"
                                >
                                    <template x-for="(subItem, subIndex) in (item.data.schema || [])" :key="subIndex">
                                        <div class="comp-wrapper">
                                            <div class="delete-btn" @click="item.data.schema.splice(subIndex, 1)"><i class="fas fa-times"></i></div>
                                            <div style="background:var(--bg-item); padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                                <label class="f-label" x-text="subItem.data.label || subItem.data.column"></label>
                                                <input type="text" class="f-input" disabled>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- FIELDSET -->
                        <template x-if="item.type === 'fieldset'">
                            <fieldset class="f-fieldset">
                                <legend><input x-model="item.data.label" style="border:none; outline:none; background:transparent; color:var(--text-main);" placeholder="Legend"></legend>
                                <div class="vb-drop-zone"
                                     style="min-height:40px;"
                                     x-data="{ isOver: false }"
                                     :class="{ 'drag-over': isOver }"
                                     @dragover.prevent.stop="isOver = true"
                                     @dragleave.prevent.stop="isOver = false"
                                     @drop.prevent.stop="isOver = false; handleDrop($event, item.data)"
                                >
                                    <template x-for="(subItem, subIndex) in (item.data.schema || [])" :key="subIndex">
                                        <div class="comp-wrapper">
                                            <div class="delete-btn" @click="item.data.schema.splice(subIndex, 1)"><i class="fas fa-times"></i></div>
                                            <div style="background:var(--bg-item); padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                                <label class="f-label" x-text="subItem.data.label || subItem.data.column"></label>
                                                <input type="text" class="f-input" disabled>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </fieldset>
                        </template>

                        <!-- TABS -->
                        <template x-if="item.type === 'tabs'">
                            <div class="f-tabs">
                                <div class="f-tabs-header">
                                    <template x-for="(tab, tIndex) in item.data.tabs" :key="tIndex">
                                        <div class="f-tab-item" @click.stop><input x-model="tab.label" style="border:none; background:transparent; width:80px; color:var(--text-main);"> <span @click="item.data.tabs.splice(tIndex, 1)" style="color:red; cursor:pointer;">&times;</span></div>
                                    </template>
                                    <div class="f-tab-item-add" @click="item.data.tabs.push({label:'New Tab', schema:[]})">+</div>
                                </div>
                                <div class="f-tab-content">
                                    <template x-for="(tab, tIndex) in item.data.tabs">
                                        <div class="vb-drop-zone" style="min-height:50px; border:1px dashed var(--border-color);"
                                             x-data="{ isOver: false }"
                                             :class="{ 'drag-over': isOver }"
                                             @dragover.prevent.stop="isOver = true"
                                             @dragleave.prevent.stop="isOver = false"
                                             @drop.prevent.stop="isOver = false; handleDrop($event, tab)"
                                        >
                                             <div x-show="!tab.schema || tab.schema.length === 0" style="color:var(--text-muted); font-size:11px;">Tab Content</div>
                                             <template x-for="(subItem, subIndex) in (tab.schema || [])" :key="subIndex">
                                                 <div class="comp-wrapper">
                                                    <div class="delete-btn" @click="tab.schema.splice(subIndex, 1)"><i class="fas fa-times"></i></div>
                                                    <div style="background:var(--bg-item); padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                                        <label class="f-label" x-text="subItem.data.label || subItem.data.column"></label>
                                                        <input type="text" class="f-input" disabled>
                                                    </div>
                                                 </div>
                                             </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- FIELD -->
                        <template x-if="item.type === 'field'">
                             <div style="background:var(--bg-item); padding:10px; border:1px solid var(--border-color); border-radius:6px;">
                                <label class="f-label" x-text="item.data.label || item.data.column"></label>
                                <template x-if="item.data.type === 'foreignId'">
                                    <select class="f-input" disabled><option>Select Relation...</option></select>
                                </template>
                                <template x-if="item.data.type !== 'foreignId'">
                                    <input type="text" class="f-input" disabled>
                                </template>
                                
                                <div x-show="item.data.type !== 'foreignId'" style="margin-top:8px; font-size:11px; border-top:1px solid var(--border-color); padding-top:4px;">
                                    <label style="display:inline-flex; align-items:center; gap:5px; cursor:pointer; color:var(--text-muted);">
                                        <input type="checkbox" x-model="item.data.is_translatable"> 
                                        <span>Translatable (getNameInputsFilament)</span>
                                    </label>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
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

                if (Alpine.data['visualFormBuilder']) return;

                Alpine.data('visualFormBuilder', ({ state, columns }) => ({
                    state: state,
                    columns: columns,
                    draggedType: null,
                    draggedData: null,
                    draggingOverRoot: false,

                    init() {
                         if (!this.state || typeof this.state !== 'object') {
                             this.state = [];
                         }
                    },

                    dragStart(e, type, data = null) {
                        this.draggedType = type;
                        this.draggedData = data;
                        e.dataTransfer.effectAllowed = 'copy';
                        e.dataTransfer.dropEffect = 'copy';
                    },

                    handleDrop(e, container, key = null) {
                        e.target.closest('.vb-drop-zone')?.classList.remove('drag-over');
                        this.draggingOverRoot = false;

                        if (!this.draggedType) return;

                        let targetArray = null;

                        if (Array.isArray(container)) {
                            targetArray = container;
                        } else if (container.schema && Array.isArray(container.schema)) {
                            targetArray = container.schema;
                        } else if (container.items && key !== null) {
                            if (!container.items[key]) container.items[key] = [];
                            targetArray = container.items[key];
                        }

                        if (targetArray) {
                            const item = this.createItem(this.draggedType, this.draggedData);
                            if (item) {
                                targetArray.push(item);
                            }
                        }
                        
                        this.draggedType = null;
                        this.draggedData = null;
                    },

                    createItem(type, data) {
                        const id = Math.random().toString(36).substr(2, 9);
                        if (type === 'grid') return { id, type, data: { columns: 2, items: {} } };
                        if (type === 'section') return { id, type, data: { label: 'Section Title', schema: [] } };
                        if (type === 'fieldset') return { id, type, data: { label: 'New Group', schema: [] } };
                        if (type === 'tabs') return { id, type, data: { tabs: [{label: 'Tab 1', schema: []}] } };
                        if (type === 'field') return { id, type, data: { column: data.name, label: data.name, type: data.type } };
                        return null;
                    }
                }));
            }

            registerConfig();
        })();
    </script>
</x-dynamic-component>
