<script setup lang="ts">
import axios from 'axios';

const createRepo = async () => {
    try {
       const response = await axios.post(route('create-repo'));
        alert(response.data.response);
        console.log(response.data)
    } catch (e) {
        console.log(e)
        alert(e);
    }
};
</script>

<template>
    <form @submit.prevent="createRepo">
        <label class="block text-gray-700 dark:text-white font-medium mb-2">Band Name</label>
        <input
            type="text"
            placeholder="Enter band name"
            class="w-full p-2 border border-gray-300 rounded-lg"
        />
        <button
            class="w-full mt-4 bg-gray-400 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-gray-400">
            Create Website
        </button>
    </form>
</template>
