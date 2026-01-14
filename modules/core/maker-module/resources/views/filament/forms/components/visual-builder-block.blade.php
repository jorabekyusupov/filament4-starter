@props(['items', 'path'])

<div 
    class="min-h-[60px] p-4 bg-gray-50/50 dark:bg-gray-800/50 rounded-md border border-dashed border-gray-300 dark:border-gray-600"
    x-data="{
        isOver: false,
        handleDrop(e) {
            this.isOver = false;
            e.stopPropagation();
            // Call parent's drop handler or modify the items array directly via Alpine magics if we are in the scope
             this.$dispatch('item-dropped', { path: '{{ $path }}' });
        }
    }"
    @dragover.prevent.stop="isOver = true"
    @dragleave.prevent.stop="isOver = false"
    @drop.prevent="handleDrop($event)"
    :class="{ 'border-primary-500 bg-primary-50 dark:bg-primary-900/10': isOver }"
>
   <!-- This approach with path strings is brittle in pure Alpine without a store. 
        Let's switch to a pure Alpine Component approach defined in a script tag that recursively renders itself?
        Alpine doesn't natively support recursive components easily without x-ignore/x-init hacks.
        
        Let's try the 'Layout Builder' approach where we simply have a fixed depth or specific types handled.
   -->
</div>
