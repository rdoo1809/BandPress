<script setup lang="ts">
defineProps({
    databaseEvents: Object,
});
</script>

<template>
    <div class="mt-4 rounded-xl bg-white p-4 shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">City Ground Events</h2>
        <div class="max-h-96 space-y-3 overflow-y-auto pr-2">
            <div v-for="(event, index) in databaseEvents" :key="index" class="rounded-lg bg-gray-100 p-3 dark:bg-gray-700">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ event.name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.day }} - {{ event.month }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.description }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.venue_link }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
