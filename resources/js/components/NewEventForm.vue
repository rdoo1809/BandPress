<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    name: '',
    day: '',
    month: '',
    description: '',
    venue_link: '',
});

const submitEvent = async () => {
    try {
        const response = await axios.post(route('new-event'), form.value);
        alert('Your Event has been added!');
        console.log(response.data)
    } catch (e) {
        console.log(e)
        alert(e);
    }
}
</script>

<template>
    <form @submit.prevent="submitEvent" class="max-w-lg mx-auto p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
            <input
                v-model="form.name"
                type="text"
                id="name"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="Event Name"
            />
        </div>

        <div>
            <label for="day" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day</label>
            <input
                v-model="form.day"
                type="text"
                id="day"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="e.g., 15"
            />
        </div>

        <div>
            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
            <input
                v-model="form.month"
                type="text"
                id="month"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="e.g., March"
            />
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea
                v-model="form.description"
                id="description"
                rows="4"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="Event details..."
            ></textarea>
        </div>

        <div>
            <label for="venueLink" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Venue Link</label>
            <input
                v-model="form.venue_link"
                type="url"
                id="venueLink"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="https://venue.com"
            />
        </div>

        <div>
            <button
                type="submit"
                class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
            >
                Add Event
            </button>
        </div>
    </form>
</template>
