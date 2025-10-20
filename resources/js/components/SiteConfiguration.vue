<script setup lang="ts">
import { ref } from 'vue';

const sections = ref([
    { id: 1, name: 'Hero Section', order: 1 },
    { id: 2, name: 'Events Section', order: 2 },
    { id: 3, name: 'Releases Section', order: 3 },
    { id: 4, name: 'About Section', order: 4 },
]);

const draggedItem = ref<number | null>(null);

const handleDragStart = (index: number) => {
    draggedItem.value = index;
};

const handleDragOver = (e: DragEvent) => {
    e.preventDefault();
};

const handleDrop = (dropIndex: number) => {
    if (draggedItem.value === null || draggedItem.value === dropIndex) return;

    const draggedSection = sections.value[draggedItem.value];
    const dropSection = sections.value[dropIndex];

    // Swap orders
    const tempOrder = draggedSection.order;
    draggedSection.order = dropSection.order;
    dropSection.order = tempOrder;

    // Sort by order
    sections.value.sort((a, b) => a.order - b.order);

    draggedItem.value = null;
};
</script>

<template>
    <div class="h-full bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 font-saira uppercase tracking-wider">
            Configure your site
        </h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6 font-saira uppercase text-sm tracking-wider">
            Drag to reorder
        </p>

        <div class="space-y-3">
            <div
                v-for="(section, index) in sections"
                :key="section.id"
                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 cursor-move border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-colors"
                draggable="true"
                @dragstart="handleDragStart(index)"
                @dragover="handleDragOver"
                @drop="handleDrop(index)"
            >
                <div class="flex items-center justify-between">
                    <span class="text-gray-900 dark:text-white font-medium">{{ section.name }}</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
            <button class="w-full bg-figma-red hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg uppercase tracking-wider font-saira transition-colors">
                Save Changes
            </button>
        </div>
    </div>
</template>
