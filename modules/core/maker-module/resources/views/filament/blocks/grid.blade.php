<div class="grid gap-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50/50 dark:bg-gray-900/50">
    @php $cols = $columns ?? 2; @endphp
    @for($i = 0; $i < $cols; $i++)
        <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 h-10 rounded flex items-center justify-center text-xs text-gray-500">
            Col {{ $i + 1 }}
        </div>
    @endfor
    <div class="col-span-full text-center text-xs text-gray-400 mt-2">
        Grid Layout ({{ $cols }} Columns) - {{ count($schema ?? []) }} Items
    </div>
</div>
