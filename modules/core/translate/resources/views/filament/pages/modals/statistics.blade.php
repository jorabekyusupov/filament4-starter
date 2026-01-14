<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach ($statistics as $module)
        <div class="group relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 p-4 transition-all hover:shadow-md">
            
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-1 rounded-full bg-primary-500 dark:bg-primary-400"></div>
                <div>
                    <h4 class="text-sm font-bold text-gray-950 dark:text-white line-clamp-1" title="{{ __($module['group']) }}">
                        {{ __($module['group']) }}
                    </h4>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $module['total_keys'] }} {{ __('keys') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col divide-y divide-gray-200 dark:divide-gray-800">
                @foreach ($module['stats'] as $stat)
                    @php
                        $percent = (float) $stat['percent'];
                        $isLow = $percent < 40;
                        $textColorClass = $isLow ? 'text-danger-600 dark:text-danger-500' : 'text-gray-950 dark:text-white';
                        $barColorClass = $isLow ? 'bg-danger-500' : 'bg-primary-600';
                    @endphp

                    <div class="py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase">
                                {{ $stat['locale'] }}
                            </span>
                            
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $stat['filled'] }}/{{ $stat['total'] }}
                                </span>
                                <span class="text-xs font-bold {{ $textColorClass }}">
                                    {{ $stat['percent'] }}%
                                </span>
                            </div>
                        </div>

                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div 
                                class="h-full rounded-full transition-all duration-500 {{ $barColorClass }}" 
                                style="width: {{ $percent }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>