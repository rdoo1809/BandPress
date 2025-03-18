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
        <button style="background-color: red">Create Website</button>
    </form>
</template>
