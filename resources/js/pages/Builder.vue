<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Builder',
        href: '/builder',
    },
];

const props = defineProps({
    bandName: String,
    contents: Object,
});

const fileFormatter = (file: any) => {
    const rawBandName = props.bandName;
    const sanitizedBandName = rawBandName?.replace(/\s+/g, '_');
    const extension = file.name.split('.').pop();
    const customName = `${sanitizedBandName}_logo.${extension}`;
    return new File([file], customName, { type: file.type });
};

const uploadLogo = async (e: any) => {
    const file = e.target.files[0];
    const renamedFile = fileFormatter(file);
    const formData = new FormData();
    formData.append('logo', renamedFile);

    try {
        await axios.post(route('stash-logo'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    } catch (error: any) {
        console.error('Upload error:', error.response?.data || error.message);
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="!props.contents?.logo">
            <h1>Upload your logo to get started!</h1>
            <input type="file" @change="uploadLogo" />
        </div>
        <div v-else>
            <img :src="`/storage/${props.contents?.logo}`"  alt="Band Logo"/>
        </div>
    </AppLayout>
</template>

<style></style>
