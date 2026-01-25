<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .db-diagram-wrapper {
            --bg-canvas: #2d2d2d; /* Dark background */
            --bg-node: #1e1e1e;   /* Darker node background */
            --border-node: #444;
            --text-main: #e0e0e0;
            --text-muted: #888;
            --primary: #3b82f6;   /* Blue accent */
            --key-pk: #fbbf24;    /* Gold for PK */
            --key-fk: #a855f7;    /* Purple for FK */
            --line-color: #666;

            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            background-color: var(--bg-canvas);
            height: 800px;
            display: flex;
            overflow: hidden;
            border-radius: 8px;
            color: var(--text-main);
            position: relative;
            user-select: none;
        }

        /* DOT GRID BACKGROUND */
        .db-canvas-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(#4b5563 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
            pointer-events: none;
        }

        /* SIDEBAR (Tools) */
        .db-sidebar {
            position: absolute;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: 50px; /* Collapsed by default basically */
            background: #333;
            border-radius: 8px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 0;
            gap: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }

        .db-tool-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #444;
            color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
        }
        .db-tool-btn:hover { background: var(--primary); color: white; }
        
        .db-sidebar-panel {
            position: absolute;
            left: 60px;
            top: 0;
            bottom: 0;
            width: 250px;
            background: #2a2a2a;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            padding: 15px;
            display: none; /* Hidden unless active */
            flex-direction: column;
            gap: 10px;
        }
        .db-sidebar-panel.active { display: flex; }

        /* NODE (Table) */
        .db-node {
            position: absolute;
            width: 280px;
            background: var(--bg-node);
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }
        .db-node-header {
            padding: 8px 12px;
            background: #333;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            border-bottom: 2px solid #3b82f6; /* Accent header */
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: grab;
        }
        .db-node-title {
            font-weight: bold;
            font-size: 14px;
            color: #fff;
        }
        .db-node-actions {
            display: flex;
            gap: 5px;
        }
        .db-action-btn {
            width: 20px; height: 20px;
            border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #888;
            font-size: 10px;
        }
        .db-action-btn:hover { background: #444; color: #fff; }
        .db-action-btn.delete:hover { background: #ef4444; }

        .db-node-body {
            padding: 5px 0;
        }

        .db-row {
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            border-bottom: 1px solid #333;
            position: relative;
        }
        .db-row:last-child { border-bottom: none; }
        .db-row:hover { background: #2a2a2a; }

        .db-col-left { display: flex; align-items: center; gap: 8px; }
        .db-col-icon { font-size: 10px; width: 14px; text-align: center; }
        .db-col-name { color: #e0e0e0; }
        .db-col-type { color: #888; text-transform: lowercase; }
        .db-col-meta { font-size: 9px; padding: 1px 4px; border-radius: 2px; background: #333; color: #aaa; margin-left: 5px;}

        /* INPUTS IN DARK MODE */
        .db-input-dark {
            background: transparent;
            border: none;
            color: inherit;
            outline: none;
            font-family: inherit;
            width: 100%;
        }
        .db-input-dark:focus { border-bottom: 1px solid var(--primary); }

        /* SVG LINES */
        .db-svg-layer {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 5;
        }
        .db-connector {
            fill: none;
            stroke: var(--line-color);
            stroke-width: 1.5;
            opacity: 0.6;
            transition: stroke 0.2s, opacity 0.2s;
        }
        .db-connector:hover {
            stroke: var(--primary);
            stroke-width: 2.5;
            opacity: 1;
            z-index: 20;
            cursor: pointer;
        }
    </style>

    <div 
        x-data="dbDiagram({
            state: $wire.entangle('{{ $getStatePath() }}'),
            availableModels: {{ json_encode($availableModels) }},
            dataTypes: {{ json_encode($dataTypes) }}
        })"
        class="db-diagram-wrapper"
        @mousedown="canvasMouseDown"
        @mousemove="canvasMouseMove"
        @mouseup="canvasMouseUp"
        @mouseleave="canvasMouseUp"
    >
        <div class="db-canvas-bg" :style="'background-position: ' + offsetX + 'px ' + offsetY + 'px'"></div>

        <!-- Line Layer (One SVG per line to avoid Alpine template-in-svg scope issues) -->
        <div class="db-svg-layer" style="pointer-events: none; z-index: 5;">
            <template x-for="(line, index) in lines" :key="index">
                <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: visible; pointer-events: none;" xmlns="http://www.w3.org/2000/svg">
                    <path :d="line.path" class="db-connector" style="pointer-events: auto;" />
                </svg>
            </template>
        </div>

        <!-- Sidebar Tools -->
        <div class="db-sidebar">
            <div class="db-tool-btn" title="Add Table" @click="addTable()"><i class="fas fa-plus"></i></div>
            <div class="db-tool-btn" title="Auto Arrange" @click="autoArrange()"><i class="fas fa-magic"></i></div>
            <div class="db-tool-btn" style="margin-top:auto;" title="Fit View" @click="resetView()"><i class="fas fa-compress-arrows-alt"></i></div>
        </div>

        <!-- Transform Container -->
        <div class="db-transform-layer is-absolute" 
             :style="'transform: translate(' + offsetX + 'px, ' + offsetY + 'px) scale(' + scale + '); position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;'">
             
            <!-- NODES (Tables) -->
            <template x-for="(table, tIndex) in state" :key="tIndex">
                <div class="db-node"
                     :style="'left:' + (table.ui_x || 100) + 'px; top:' + (table.ui_y || 100) + 'px; pointer-events:auto;'"
                     :id="'node-' + tIndex"
                >
                    <!-- Header -->
                    <div class="db-node-header" @mousedown.stop="nodeMouseDown($event, tIndex)">
                        <input type="text" x-model="table.name" class="db-input-dark db-node-title" placeholder="table_name">
                        <div class="db-node-actions">
                            <div class="db-action-btn" title="Toggle Settings" @click="table.showSettings = !table.showSettings"><i class="fas fa-cog"></i></div>
                            <div class="db-action-btn delete" title="Delete Table" @click="state.splice(tIndex, 1); updateLines()"><i class="fas fa-times"></i></div>
                        </div>
                    </div>

                    <!-- Options Panel (Toggleable) -->
                    <div x-show="table.showSettings" style="padding:10px; background:#252525; border-bottom:1px solid #444; font-size:11px;">
                         <label style="display:block; margin-bottom:5px;"><input type="checkbox" x-model="table.has_resource"> Generate Resource</label>
                         <label style="display:block; margin-bottom:5px;"><input type="checkbox" x-model="table.soft_deletes"> Soft Deletes</label>
                         <label style="display:block; margin-bottom:5px;"><input type="checkbox" x-model="table.logged"> Loggable (User Tracking)</label>
                    </div>

                    <!-- Columns -->
                    <div class="db-node-body">
                        <!-- Add Column Input -->
                         <div class="db-row" style="background:rgba(255,255,255,0.02);" x-data="{ newName: '', newType: 'string' }">
                            <div class="db-col-left" style="flex:1;">
                                <i class="fas fa-plus db-col-icon" :class="{ 'text-primary': newName }"></i>
                                <input type="text" 
                                       x-model="newName"
                                       placeholder="Add column..." 
                                       class="db-input-dark" 
                                       style="font-style:italic;"
                                       @keydown.enter.prevent="if(newName) { addColumn(table, newName, newType); newName = ''; }"
                                >
                            </div>
                            <!-- Type Select for New Column -->
                            <select x-model="newType" class="db-input-dark" style="width:70px; text-align:right; font-size:11px; color:#aaa; margin-right:5px; cursor:pointer;">
                                <template x-for="(lbl, val) in dataTypes" :key="val">
                                    <option :value="val" x-text="lbl"></option>
                                </template>
                            </select>
                            <!-- Add Button -->
                            <div class="db-action-btn" 
                                 x-show="newName.length > 0" 
                                 @click="addColumn(table, newName, newType); newName = '';"
                                 style="color: #22c55e;"
                            >
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <!-- Visual Only ID Row -->
                        <div class="db-row" style="opacity: 0.7; pointer-events: none; border-bottom: 1px dashed #444;">
                            <div class="db-col-left">
                                <i class="fas fa-key db-col-icon" style="color:var(--key-pk)"></i>
                                <span class="db-col-name" style="width:100px; padding-left:9px; font-weight:bold; font-size:12px;">id</span>
                            </div>
                            <div style="display:flex; align-items:center;">
                                <span style="font-size:11px; color:#666; width:80px; text-align:right;">auto_increment</span>
                                <div style="width:20px; margin-left:5px;"></div> <!-- Spacer for alignment -->
                            </div>
                        </div>

                        <template x-for="(col, cIndex) in table.columns" :key="cIndex">
                            <div class="db-row" :id="'col-' + tIndex + '-' + cIndex">
                                <div class="db-col-left">
                                    <!-- Icon based on type -->
                                    <template x-if="col.type === 'foreignId'"><i class="fas fa-link db-col-icon" style="color:var(--key-fk)"></i></template>
                                    <template x-if="col.type !== 'foreignId'"><i class="far fa-circle db-col-icon" style="font-size:6px;"></i></template>
                                    
                                    <!-- Name -->
                                    <input type="text" x-model="col.name" class="db-input-dark db-col-name" style="width:100px;">
                                </div>
                                
                                <div style="display:flex; align-items:center; position:relative;" class="group">
                                    <!-- Type -->
                                    <select x-model="col.type" class="db-input-dark db-col-type" style="width:80px; text-align:right; cursor:pointer;" @change="updateLines()">
                                        <template x-for="(lbl, val) in dataTypes" :key="val">
                                            <option :value="val" x-text="lbl" :selected="col.type === val"></option>
                                        </template>
                                    </select>
                                    <i class="fas fa-chevron-down" style="font-size:8px; color:#666; pointer-events:none; margin-left:4px;"></i>
                                    
                                    <!-- Settings / Options -->
                                    <div class="db-action-btn" style="margin-left:5px; color:#666;" @click.stop="col.showOptions = !col.showOptions" title="Column Options"><i class="fas fa-cog"></i></div>

                                    <!-- Delete -->
                                    <div class="db-action-btn delete" style="margin-left:2px; opacity:1; cursor:pointer;" @click.stop="table.columns.splice(cIndex, 1); updateLines()">&times;</div>
                                </div>
                                
                                <!-- Column Options Popup -->
                                <div x-show="col.showOptions" @click.outside="col.showOptions = false" style="position:absolute; right: -140px; top: 0; width: 130px; background: #333; border: 1px solid #555; border-radius: 4px; padding: 8px; z-index: 50; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                                     <div style="font-size:10px; font-weight:bold; color:#888; margin-bottom:5px; border-bottom:1px solid #444; padding-bottom:2px;">COLUMN OPTIONS</div>
                                     
                                     <label style="display:flex; align-items:center; gap:5px; margin-bottom:5px; font-size:11px; color:#ddd; cursor:pointer;">
                                         <input type="checkbox" x-model="col.nullable"> Nullable
                                     </label>
                                     <label style="display:flex; align-items:center; gap:5px; margin-bottom:5px; font-size:11px; color:#ddd; cursor:pointer;">
                                         <input type="checkbox" x-model="col.unique"> Unique
                                     </label>
                                     <label style="display:flex; align-items:center; gap:5px; margin-bottom:5px; font-size:11px; color:#ddd; cursor:pointer;">
                                         <input type="checkbox" x-model="col.index"> Index
                                     </label>
                                     <template x-if="['string', 'text', 'json'].includes(col.type)">
                                         <label style="display:flex; align-items:center; gap:5px; font-size:11px; color:#ddd; cursor:pointer;">
                                             <input type="checkbox" x-model="col.is_translatable"> Translatable
                                         </label>
                                     </template>

                                     <!-- Relation Config (only if FK) -->
                                     <template x-if="col.type === 'foreignId'">
                                         <div style="margin-top:8px; border-top:1px solid #444; padding-top:5px;">
                                             <div style="font-size:9px; color:#888; margin-bottom:2px;">RELATED MODEL</div>
                                             <select x-model="col.related_model" class="db-input-dark" style="background:#222; border:1px solid #444; padding:2px; font-size:10px; width:100%; border-radius:3px;" @change="updateLines()">
                                                 <option value="">Select...</option>
                                                 <template x-for="opt in getRelationOptions(table.name)" :key="opt.value + opt.label">
                                                     <option :value="opt.value" x-text="opt.label" :disabled="opt.disabled" :style="opt.style" :selected="col.related_model === opt.value"></option>
                                                 </template>
                                             </select>
                                             
                                             <div style="font-size:9px; color:#888; margin-top:5px; margin-bottom:2px;">RELATED COLUMN</div>
                                             <input type="text" x-model="col.related_column" class="db-input-dark" style="background:#222; border:1px solid #444; padding:2px 5px; font-size:10px; width:100%; border-radius:3px;" placeholder="name">
                                         </div>
                                     </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        (function() {
            function registerDiagram() {
                if (typeof Alpine === 'undefined') {
                    document.addEventListener('alpine:init', registerDiagram);
                    return;
                }

                if (Alpine.data['dbDiagram']) return;

                Alpine.data('dbDiagram', ({ state, availableModels, dataTypes }) => ({
                    state: state,
                    availableModels: availableModels,
                    dataTypes: dataTypes,
                    
                    // Canvas State
                    offsetX: 0,
                    offsetY: 0,
                    scale: 1,
                    isDraggingCanvas: false,
                    lastMouseX: 0,
                    lastMouseY: 0,
                    
                    // Node Dragging State
                    draggedNodeIndex: null,

                    // Lines
                    lines: [],

                    init() {
                         if (!this.state || typeof this.state !== 'object') {
                             this.state = [];
                         }
                         // Init positions and cleanup ID
                         this.state.forEach((table, i) => {
                             if(!table.ui_x) table.ui_x = 50 + (i * 300);
                             if(!table.ui_y) table.ui_y = 50 + (i * 50);
                             if(!table.columns) table.columns = [];
                             // Remove ID if present in state (legacy fix)
                             table.columns = table.columns.filter(c => c.name !== 'id');
                         });

                         // Defer line drawing until DOM is ready
                         this.$nextTick(() => { 
                             setTimeout(() => this.updateLines(), 100);
                         });

                         // Watch for changes to update lines
                         this.$watch('state', () => { setTimeout(() => this.updateLines(), 0); });
                    },

                    addTable() {
                        this.state.push({
                            name: 'new_table_' + (this.state.length + 1),
                            has_resource: true,
                            soft_deletes: false,
                            logged: false,
                            status: true,
                            ui_x: 200 - this.offsetX, 
                            ui_y: 200 - this.offsetY,
                            columns: [] // No default ID
                        });
                        this.$nextTick(() => this.updateLines());
                    },

                    addColumn(table, name, type = 'string') {
                        if(!name) return;
                        table.columns.push({
                            name: name,
                            type: type,
                            nullable: false,
                            unique: false,
                            index: false,
                            is_translatable: false,
                            related_model: '', // Would need logic to guess from name
                            related_column: 'name'
                        });
                        this.updateLines();
                    },

                    // CANVAS INPUT HANDLERS
                    canvasMouseDown(e) {
                        if(e.button === 0) { // Left click
                            if(e.target.classList.contains('db-diagram-wrapper') || e.target.classList.contains('db-canvas-bg')) {
                                this.isDraggingCanvas = true;
                                this.lastMouseX = e.clientX;
                                this.lastMouseY = e.clientY;
                            }
                        }
                    },

                    nodeMouseDown(e, index) {
                        this.draggedNodeIndex = index;
                        this.lastMouseX = e.clientX;
                        this.lastMouseY = e.clientY;
                    },

                    canvasMouseMove(e) {
                         const dx = e.clientX - this.lastMouseX;
                         const dy = e.clientY - this.lastMouseY;

                         if (this.isDraggingCanvas) {
                             this.offsetX += dx;
                             this.offsetY += dy;
                             this.lastMouseX = e.clientX;
                             this.lastMouseY = e.clientY;
                         } else if (this.draggedNodeIndex !== null) {
                             const table = this.state[this.draggedNodeIndex];
                             table.ui_x = (table.ui_x || 0) + dx;
                             table.ui_y = (table.ui_y || 0) + dy;
                             this.lastMouseX = e.clientX;
                             this.lastMouseY = e.clientY;
                             this.updateLines();
                         }
                    },

                    canvasMouseUp(e) {
                        this.isDraggingCanvas = false;
                        this.draggedNodeIndex = null;
                    },

                    resetView() {
                        this.offsetX = 0;
                        this.offsetY = 0;
                        this.scale = 1;
                    },
                    
                    autoArrange() {
                         // Simple grid layout
                         let col = 0;
                         let row = 0;
                         this.state.forEach((table, i) => {
                             table.ui_x = 50 + (col * 350);
                             table.ui_y = 50 + (row * 300);
                             col++;
                             if(col > 2) { col = 0; row++; }
                         });
                         this.updateLines();
                    },

                    getRelationOptions(currentTableName) {
                        let options = [];
                        
                        // Canvas Tables
                        options.push({ label: '-- Canvas Tables --', value: '', disabled: true, style: 'font-weight:bold; background:#333; color:#aaa;' });
                        this.state.forEach(t => {
                            if (t.name !== currentTableName) {
                                options.push({ label: t.name, value: t.name, disabled: false });
                            }
                        });

                        // System Models
                        options.push({ label: '-- System Models --', value: '', disabled: true, style: 'font-weight:bold; background:#333; color:#aaa;' });
                        // dataTypes/availableModels are objects. Alpine/JS iterates keys.
                        // availableModels: { 'User': 'App\Models\User' }
                        for (const [name, path] of Object.entries(this.availableModels)) {
                            options.push({ label: name, value: name, disabled: false });
                        }
                        
                        return options;
                    },

                    // RELATIONSHIP LINES
                    updateLines() {
                        const newLines = [];
                        
                        // Ensure state is array
                        if (!Array.isArray(this.state)) return;

                        this.state.forEach((table, tIndex) => {
                            if (!table.columns) return;

                            table.columns.forEach((col, cIndex) => {
                                if (col.type !== 'foreignId') return;

                                // Resolve Target
                                let targetTableIndex = -1;
                                
                                // 0. Explicit Canvas Table Match
                                if (col.related_model) {
                                    const explicitIndex = this.state.findIndex(t => t.name === col.related_model);
                                    if (explicitIndex !== -1) {
                                        targetTableIndex = explicitIndex;
                                    }
                                }

                                // 1. Try by related_model (System Models - heuristic)
                                if (targetTableIndex === -1 && col.related_model) {
                                    const pluralGuess = col.related_model.toLowerCase() + 's';
                                    targetTableIndex = this.state.findIndex(t => t.name.toLowerCase() === pluralGuess);
                                }

                                // 2. Heuristic by column name (e.g. user_id -> users)
                                if (targetTableIndex === -1 && col.name && col.name.endsWith('_id')) {
                                     const likelyTableName = col.name.replace('_id', '') + 's'; // simple plural
                                     targetTableIndex = this.state.findIndex(t => t.name === likelyTableName);
                                     
                                     // Try singular
                                     if(targetTableIndex === -1) {
                                         targetTableIndex = this.state.findIndex(t => t.name === likelyTableName.slice(0, -1));
                                     }
                                }

                                if (targetTableIndex !== -1 && targetTableIndex !== tIndex) {
                                    const sourceNode = document.getElementById('node-' + tIndex);
                                    const targetNode = document.getElementById('node-' + targetTableIndex);
                                    
                                    if (sourceNode && targetNode) {
                                        // DOM Rects approach is safer for absolute positioning relative to scrolled canvas
                                        // But we are using explicit x/y from state.
                                        
                                        const sX = (this.state[tIndex].ui_x || 0) + 280; // Width of node
                                        const sY = (this.state[tIndex].ui_y || 0) + 40 + (cIndex * 37); // Header + row height estimate (visual tweaking needed)
                                        
                                        const tX = (this.state[targetTableIndex].ui_x || 0);
                                        const tY = (this.state[targetTableIndex].ui_y || 0) + 40; // Attach to header-ish area

                                        // Bezier Control Points
                                        const cp1X = sX + 50;
                                        const cp1Y = sY;
                                        const cp2X = tX - 50;
                                        const cp2Y = tY;
                                        
                                        const offX = this.offsetX;
                                        const offY = this.offsetY;
                                        
                                        const finalPath = `M ${sX + offX} ${sY + offY} C ${cp1X + offX} ${cp1Y + offY}, ${cp2X + offX} ${cp2Y + offY}, ${tX + offX} ${tY + offY}`;

                                        newLines.push({ path: finalPath });
                                    }
                                }
                            });
                        });
                        
                        this.lines = newLines;
                    }

                }));
            }

            registerDiagram();
        })();
    </script>
</x-dynamic-component>
