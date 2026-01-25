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
            height: 850px;
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
            width: 300px; /* Slightly wider sidebar */
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
            max-width: 100%;

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

        /* Settings Button */
        .settings-btn {
            position: absolute;
            top: -12px;
            right: 18px; /* Next to delete btn */
            width: 26px;
            height: 26px;
            background: #3b82f6; /* Blue */
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
        .comp-wrapper:hover > .settings-btn { display: flex; }

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
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'wizard')">
                    <i class="fas fa-hat-wizard"></i> Wizard
                </div>
                <!-- Split (Columns) is similar to Grid but typically just 2 side-by-side or specific ratios. 
                     For now let's use Grid as advanced, but if user wants Split, we can alias it or make a specific "Split" UI. 
                     The user asked for Split. Let's add it as a simplified 2-col. -->
                <div class="vb-draggable-item" draggable="true" @dragstart="dragStart($event, 'split')">
                    <i class="fas fa-columns"></i> Split
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
                        <!-- Settings Button -->
                        <div class="settings-btn" @click="openSettings(item)"><i class="fas fa-cog"></i></div>

                        <!-- GRID -->
                        <template x-if="item.type === 'grid'">
                            <div class="f-grid-container">
                                <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; font-weight:bold; margin-bottom:5px;">Grid</div>
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
                                                    <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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

                        <!-- SPLIT -->
                        <template x-if="item.type === 'split'">
                            <div class="f-grid-container" style="border-style:solid; border-width:1px;">
                                <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; font-weight:bold; margin-bottom:5px;">Split</div>
                                <div class="f-grid-row" style="grid-template-columns: 1fr 1fr;"> <!-- Always 2 cols for split default -->
                                    <template x-for="colIndex in 2">
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
                                                     <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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
                                            <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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
                                            <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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
                                                    <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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

                        <!-- WIZARD -->
                        <template x-if="item.type === 'wizard'">
                            <div class="f-tabs" style="border-color:var(--primary);">
                                <div class="f-tabs-header" style="background:rgba(217, 119, 6, 0.05);">
                                    <div style="padding:10px; font-weight:bold; font-size:11px; text-transform:uppercase; color:var(--primary);"><i class="fas fa-hat-wizard"></i> Wizard</div>
                                    <template x-for="(step, sIndex) in item.data.steps" :key="sIndex">
                                        <div class="f-tab-item" @click.stop>
                                            <span style="font-weight:bold; margin-right:4px;" x-text="sIndex + 1 + '.'"></span>
                                            <input x-model="step.label" style="border:none; background:transparent; width:80px; color:var(--text-main);"> 
                                            <span @click="item.data.steps.splice(sIndex, 1)" style="color:red; cursor:pointer;">&times;</span>
                                        </div>
                                    </template>
                                    <div class="f-tab-item-add" @click="item.data.steps.push({label:'New Step', schema:[]})">+</div>
                                </div>
                                <div class="f-tab-content">
                                    <template x-for="(step, sIndex) in item.data.steps">
                                        <div class="vb-drop-zone" style="min-height:50px; border:1px dashed var(--border-color); margin-bottom:10px;"
                                             x-data="{ isOver: false }"
                                             :class="{ 'drag-over': isOver }"
                                             @dragover.prevent.stop="isOver = true"
                                             @dragleave.prevent.stop="isOver = false"
                                             @drop.prevent.stop="isOver = false; handleDrop($event, step)"
                                        >
                                             <div style="font-size:10px; color:var(--text-muted); margin-bottom:5px;">Step <span x-text="sIndex+1"></span> Content</div>
                                             <template x-for="(subItem, subIndex) in (step.schema || [])" :key="subIndex">
                                                 <div class="comp-wrapper">
                                                    <div class="delete-btn" @click="step.schema.splice(subIndex, 1)"><i class="fas fa-times"></i></div>
                                                    <div class="settings-btn" @click="openSettings(subItem)"><i class="fas fa-cog"></i></div>
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
                                
                                <!-- Foreign ID (Select) -->
                                <template x-if="item.data.type === 'foreignId'">
                                    <select class="f-input" disabled><option>Select Relation...</option></select>
                                </template>

                                <!-- Boolean (Toggle) -->
                                <template x-if="item.data.type === 'boolean'">
                                    <div style="display:flex; align-items:center; gap:10px; margin-top:5px;">
                                        <div style="width:36px; height:20px; background:var(--primary); border-radius:20px; position:relative; opacity:0.5;">
                                            <div style="width:16px; height:16px; background:white; border-radius:50%; position:absolute; top:2px; right:2px;"></div>
                                        </div>
                                        <span style="font-size:12px; color:var(--text-muted);">Toggle</span>
                                    </div>
                                </template>

                                <!-- Textarea (text, longText, json) -->
                                <template x-if="['text', 'mediumText', 'longText', 'json', 'jsonb'].includes(item.data.type)">
                                    <textarea class="f-input" disabled style="height:60px; resize:none;"></textarea>
                                </template>

                                <!-- Date/Time -->
                                <template x-if="['date', 'datetime', 'timestamp'].includes(item.data.type)">
                                    <div style="position:relative;">
                                        <input type="text" class="f-input" disabled placeholder="YYYY-MM-DD">
                                        <i class="fas fa-calendar" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
                                    </div>
                                </template>

                                <!-- Numeric -->
                                <template x-if="['integer', 'tinyInteger', 'smallInteger', 'mediumInteger', 'bigInteger', 'unsignedInteger', 'unsignedBigInteger', 'decimal', 'float', 'double'].includes(item.data.type)">
                                     <input type="number" class="f-input" disabled placeholder="0">
                                </template>

                                <!-- Default Text Input -->
                                <template x-if="!['foreignId', 'boolean', 'text', 'mediumText', 'longText', 'json', 'jsonb', 'date', 'datetime', 'timestamp', 'integer', 'tinyInteger', 'smallInteger', 'mediumInteger', 'bigInteger', 'unsignedInteger', 'unsignedBigInteger', 'decimal', 'float', 'double'].includes(item.data.type)">
                                    <input type="text" class="f-input" disabled>
                                </template>
                                
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; font-size:10px; color:var(--text-muted);">
                                    <div x-show="item.data.type !== 'foreignId'">
                                         <label style="display:inline-flex; align-items:center; gap:5px; cursor:pointer;">
                                            <input type="checkbox" x-model="item.data.is_translatable"> 
                                            <span>Translatable</span>
                                        </label>
                                    </div>
                                    <div style="font-style:italic;" x-show="item.data.required">
                                        <i class="fas fa-asterisk" style="font-size:8px; color:red;"></i> Required
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
                </template>
            </div>
        </div>

        <!-- SETTINGS MODAL -->
        <template x-teleport="body">
            <div x-show="modalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-cloak>
                <div class="w-full max-w-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl transform transition-all" @click.away="modalOpen = false">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                            Component Settings
                        </h3>
                        <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                
                    <template x-if="selectedItem">
                        <div class="p-6 flex flex-col gap-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            
                            <!-- Label -->
                            <div x-show="selectedItem.data.label !== undefined">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Label</label>
                                <input type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.label">
                                <label class="inline-flex items-center gap-2 mt-2 cursor-pointer">
                                    <input type="checkbox" x-model="selectedItem.data.translate_label" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Translate Key</span>
                                </label>
                            </div>

                            <!-- Column Span -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Column Span</label>
                                <select class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.column_span">
                                    <option value="">Default</option>
                                    <option value="1">1 Column</option>
                                    <option value="2">2 Columns</option>
                                    <option value="3">3 Columns</option>
                                    <option value="4">4 Columns</option>
                                    <option value="full">Full Width</option>
                                </select>
                            </div>

                            <!-- Field Specifics -->
                            <template x-if="selectedItem.type === 'field'">
                                <div class="flex flex-col gap-5">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" x-model="selectedItem.data.required" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"> 
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Required Field</span>
                                    </label>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Placeholder</label>
                                        <input type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.placeholder">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Helper Text</label>
                                        <input type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.helper_text">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Hint</label>
                                        <input type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.hint">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Default Value</label>
                                        <input type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white text-sm" x-model="selectedItem.data.default">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="button" @click="saveSettings()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </template>
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
                    modalOpen: false,
                    selectedItem: null,

                    init() {
                         if (!this.state || typeof this.state !== 'object') {
                             this.state = [];
                         }
                    },

                    openSettings(item) {
                        this.selectedItem = item;
                        if (!this.selectedItem.data) this.selectedItem.data = {};
                        this.modalOpen = true;
                    },

                    saveSettings() {
                        this.modalOpen = false;
                        // Auto-saved via x-model, but we can do extra logic here
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
                        if (type === 'split') return { id, type, data: { columns: 2, items: {} } }; // Same as grid for now structure-wise, distinguished by type
                        if (type === 'section') return { id, type, data: { label: 'Section Title', schema: [] } };
                        if (type === 'fieldset') return { id, type, data: { label: 'New Group', schema: [] } };
                        if (type === 'tabs') return { id, type, data: { tabs: [{label: 'Tab 1', schema: []}] } };
                        if (type === 'wizard') return { id, type, data: { steps: [{label: 'Step 1', schema: []}] } };
                        if (type === 'field') {
                         return { 
                             id, 
                             type, 
                             data: { 
                                 column: data.name, 
                                 label: data.name, 
                                 type: data.type,
                                 is_translatable: false,
                                 related_column: 'name',
                                 required: false,
                                 column_span: null
                             } 
                        };
                    }
                        return null;
                    }
                }));
            }

            registerConfig();
        })();
    </script>
</x-dynamic-component>
