<script setup lang="ts">
import { ref } from 'vue';

const events = ref([
    { title: "White Rabbit", date: "March 8", description: "Acoustic gig at White Rabbit!" },
    { title: "Room47", date: "March 22", description: "Get your socks rocked off!" },
    { title: "Out of the Blues", date: "March 26", description: "Toronto debut! Click for tickets." },
    { title: "Jazz Night", date: "April 5", description: "Join us for a jazzy evening." },
    { title: "Summer Fest", date: "June 10", description: "Outdoor festival with live performances!" },
    { title: "Rock Revival", date: "July 15", description: "Classic rock night at the stadium!" },
]);
</script>

<template>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Upcoming Events</h2>

        <div class="max-h-96 overflow-y-auto space-y-3 pr-2">
            <div v-for="(event, index) in events" :key="index" class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ event.title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.date }} - {{ event.description }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: Custom scrollbar styling */
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
