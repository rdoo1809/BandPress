<script setup lang="ts">
defineProps({
    databaseReleases: Object,
});
</script>

<template>
    <div class="mt-4 rounded-xl bg-white p-4 shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Releases</h2>
        <div class="max-h-96 space-y-3 overflow-y-auto pr-2">
            <div v-for="(release, index) in databaseReleases" :key="index" class="rounded-lg bg-gray-100 p-3 dark:bg-gray-700 flex justify-center items-center">
                <img class="max-h-16" :src="release.cover_image"  alt="release album art"/>
                <h3 class="text-md font-medium ml-2 text-gray-900 dark:text-gray-100">{{ release.host_link }}</h3>
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
