<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Builder',
        href: '/builder',
    },
];

const props = defineProps({
    bandName: String,
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

    // const res = await axios.post(route('stash-logo'), formData);
    // console.log(formData);
    for (const [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <h1>Upload your logo to get started!</h1>
        <input type="file" @change="uploadLogo" />
    </AppLayout>
</template>

<style></style>
