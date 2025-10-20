<script setup lang="ts">
defineProps<{
    liveUrl?: string;
}>();
</script>

<template>
    <div class="h-full bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 font-saira uppercase tracking-wider">
            Site Preview
        </h2>

        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 h-64 flex items-center justify-center">
            <div v-if="liveUrl" class="w-full h-full">
                <iframe
                    :src="liveUrl"
                    class="w-full h-full rounded-lg border-0"
                    title="Site Preview"
                ></iframe>
            </div>
            <div v-else class="text-center text-gray-500 dark:text-gray-400">
                <div class="text-lg font-medium mb-2">No preview available</div>
                <div class="text-sm">Create your site first to see a preview</div>
            </div>
        </div>
    </div>
</template>
