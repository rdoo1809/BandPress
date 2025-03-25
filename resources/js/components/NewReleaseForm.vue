<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    coverImage: null,
    hostLink: '',
});

const showForm = ref(false);
const toggleForm = () => {
    showForm.value = !showForm.value;
};

const submitRelease = async () => {
    const formData = new FormData();
    formData.append('hostLink', form.value.hostLink);
    if (form.value.coverImage) {
        formData.append('image', form.value.coverImage);
    }

    try {
        const response = await axios.post(route(''), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        console.log(response.data);
        alert('Your Event has been added!');
    } catch (e) {
        console.error(e);
        alert(e);
    }
};

const handleFileChange = (event: any) => {
    form.value.coverImage = event.target.files[0]; // Get the file from the input
};
</script>

<template>
    <div class="max-w-lg mx-auto">
        <button
            @click="toggleForm"
            class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
        >
            {{ showForm ? "Hide Release Form" : "Add Release" }}
        </button>

        <transition name="fade">
            <form v-if="true" @submit.prevent="submitRelease"
                  class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md space-y-6">

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Album Artwork</label>
                    <input type="file" id="image" required
                           @change="handleFileChange" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"/>

                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input
                        v-model="form.hostLink"
                        type="text"
                        id="name"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        placeholder="paste link to release"
                    />
                </div>


                <div>
                    <button
                        type="submit"
                        class="w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-500 dark:hover:bg-green-600"
                    >
                        Submit Release
                    </button>
                </div>
            </form>
        </transition>
    </div>
</template>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease-in-out;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
