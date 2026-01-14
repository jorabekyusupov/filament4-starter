<div class="fi-tabs flex flex-col gap-y-4">
    <div class="fi-tabs-header-ctn flex items-center gap-x-4 overflow-x-auto">
        <nav class="fi-tabs-header flex gap-x-1" aria-label="Tabs" role="tablist">
            @foreach($tabs ?? [] as $tab)
                <div class="fi-tabs-item flex items-center justify-center gap-x-2 rounded-lg px-3 py-2 text-sm font-medium transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 bg-gray-50 dark:bg-white/5 text-primary-600 dark:text-primary-400">
                    {{ $tab['label'] ?? 'New Tab' }}
                </div>
            @endforeach
            @if(empty($tabs))
                <div class="text-xs text-gray-500 px-3 py-2">No Tabs</div>
            @endif
        </nav>
    </div>
    <div class="p-4 border border-gray-200 dark:border-white/10 rounded-lg">
        <span class="text-xs text-gray-500">Tab Content Area</span>
    </div>
</div>
