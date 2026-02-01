<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div>
    <style>
        .lm-grid {
            display: grid;
            gap: 10px;
            align-items: center;
        }
        .lm-header {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding-bottom: 5px;
            border-bottom: 1px solid var(--border-color);
        }
        .lm-cell {
            padding: 5px;
        }
    </style>

    <div
        x-data="languageManager({
            state: $wire.entangle('{{ $getStatePath() }}'),
            locales: {{ json_encode($locales) }},
            localeLabels: {{ json_encode($localeLabels) }},
            defaultLocale: '{{ app()->getLocale() }}'
        })"
        class="table-builder-wrapper"
        style="flex-direction: column; height: auto; padding: 20px; overflow: visible;"
    >
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h3 style="margin:0; font-weight:bold; font-size:18px;">Translations</h3>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <!-- Manual Add -->
                <div style="display: flex; gap: 5px;">
                    <input type="text" x-model="newKey" @keydown.enter="addManualKey()" placeholder="Add custom key..." style="padding: 5px 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px; width: 150px;">
                    <button type="button" @click="addManualKey()" style="background: var(--bg-sidebar); border: 1px solid var(--border-color); padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <div style="width: 1px; height: 20px; background: var(--border-color); margin: 0 5px;"></div>

                <div x-text="scanStatus" style="font-size: 13px; color: var(--primary); font-family: monospace;"></div>
                <button type="button" @click="scanKeys()" style="background: var(--primary); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 13px;">
                    <i class="fas fa-sync"></i> Scan Keys
                </button>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <div class="lm-grid" :style="'grid-template-columns: 200px ' + 'repeat(' + locales.length + ', minmax(200px, 1fr));'">
                
                <!-- Headers -->
                <div class="lm-header">Key</div>
                <template x-for="code in locales" :key="code">
                    <div class="lm-header" x-text="localeLabels[code] || code"></div>
                </template>

                <!-- Rows -->
                <template x-for="key in keys" :key="key">
                    <div style="display: contents;">
                        <!-- Key Column -->
                        <div class="lm-cell" style="font-size: 13px; font-weight: 500; word-break: break-all;" x-text="key"></div>

                        <!-- Locale Columns -->
                        <template x-for="code in locales" :key="code">
                            <div class="lm-cell">
                                <input 
                                    type="text" 
                                    x-model="state[key][code]" 
                                    :placeholder="localeLabels[code]"
                                    :required="code === defaultLocale"
                                    style="width: 100%; padding: 6px; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-item); color: var(--text-main); font-size: 13px;"
                                >
                            </div>
                        </template>
                    </div>
                </template>

                <div x-show="keys.length === 0" style="grid-column: 1 / -1; text-align: center; padding: 20px; color: var(--text-muted); font-style: italic;">
                    No translatable keys found. Click "Scan Keys" to search.
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const initLanguageManager = () => {
                if (Alpine.data('languageManager')) return; // Prevent re-registration

                Alpine.data('languageManager', ({ state, locales, localeLabels, defaultLocale }) => ({
                    state: state,
                    locales: locales,
                    localeLabels: localeLabels,
                    defaultLocale: defaultLocale,
                    keys: [],
                    newKey: '',
                    scanStatus: '',

                    init() {
                        if (!this.state || Array.isArray(this.state)) {
                            this.state = {};
                        }
                        // We do NOT call scanKeys on init automatically anymore to avoid performance hit or empty data issues on load.
                        // User can click "Scan" manually. But if we want auto-load existing keys:
                        this.loadExistingKeys();
                    },

                    loadExistingKeys() {
                        if (this.state) {
                            this.keys = Object.keys(this.state).sort();
                        }
                    },

                    addManualKey() {
                        const k = this.newKey.trim();
                        if (!k) return;
                        
                        if (this.keys.includes(k)) {
                            alert('Key already exists');
                            return;
                        }

                        this.keys.push(k);
                        this.keys.sort();
                        
                        if (!this.state[k]) {
                            this.state[k] = {};
                        }
                        
                        this.newKey = '';
                    },

                    async scanKeys() {
                        this.scanStatus = 'Scanning...';
                        let foundKeys = new Set();
                        if (this.keys) this.keys.forEach(k => foundKeys.add(k));

                        const add = (k) => {
                            if (k && typeof k === 'string' && k.trim() !== '') {
                                foundKeys.add(k.trim());
                            }
                        };

                        try {
                            // Fetch entire data object to be safe
                            const formData = await this.$wire.get('data');
                            console.log('FormData:', formData);

                            if (!formData) {
                                this.scanStatus = 'Error: No data found';
                                return;
                            }

                            // 1. Resource Layouts
                            const rData = formData.resource_layouts;
                            if (rData && Array.isArray(rData)) {
                                rData.forEach(layout => {
                                    add(layout.model_label);
                                    add(layout.plural_model_label);
                                    add(layout.navigation_label);
                                    add(layout.navigation_group);
                                    
                                    // Visual Builder Schema structure:
                                    // layout.schema might be the direct array or an object depending on how it's saved.
                                    // Based on VB implementation: 'schema' stores the whole JSON/Array state.
                                    // Usually: schema = { columns: [...], settings: ... }
                                    
                                    let schema = layout.schema;
                                    // If schema is JSON string, parse it
                                    if (typeof schema === 'string') {
                                        try { schema = JSON.parse(schema); } catch(e) {}
                                    }

                                    if (schema && Array.isArray(schema.columns)) {
                                        schema.columns.forEach(col => {
                                            if (col.fields && Array.isArray(col.fields)) {
                                                col.fields.forEach(f => {
                                                    // Field data might be nested in 'data' prop if utilizing the builder format
                                                    let data = f.data || f; 
                                                    add(data.label);
                                                    add(data.placeholder);
                                                    add(data.helperText);
                                                    add(data.hint);
                                                    if (data.options && Array.isArray(data.options)) {
                                                        data.options.forEach(opt => add(opt.label));
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                            
                            // 2. Table Layouts
                            const tData = formData.table_layouts;
                             if (tData && Array.isArray(tData)) {
                                tData.forEach(layout => {
                                    let schema = layout.schema;
                                    if (typeof schema === 'string') {
                                        try { schema = JSON.parse(schema); } catch(e) {}
                                    }

                                    if (schema && Array.isArray(schema.columns)) {
                                         schema.columns.forEach(c => {
                                             if (c.is_label_translated && c.label) add(c.label);
                                             if (c.is_tooltip_translated && c.tooltip) add(c.tooltip);
                                         });
                                    }
                                    if (schema && Array.isArray(schema.filters)) {
                                         schema.filters.forEach(f => {
                                             add(f.label);
                                             add(f.placeholder); 
                                         });
                                    }
                                });
                            }
                            
                            this.syncState(foundKeys);
                            this.scanStatus = `Scanned. Found ${foundKeys.size} keys.`;
                            
                        } catch (e) {
                            console.error('Scan failed:', e);
                            this.scanStatus = 'Error: ' + e.message;
                        }
                    },

                    syncState(foundKeys) {
                        this.keys = Array.from(foundKeys).sort();
                        
                        this.keys.forEach(key => {
                            if (!this.state[key]) {
                                this.state[key] = {};
                                // Pre-fill default locale with key name if empty? No, leave empty.
                            }
                        });
                    }
                }));
            };

            if (typeof Alpine !== 'undefined') {
                initLanguageManager();
            } else {
                document.addEventListener('alpine:init', initLanguageManager);
            }
        })();
    </script>
    </div>
</x-dynamic-component>
