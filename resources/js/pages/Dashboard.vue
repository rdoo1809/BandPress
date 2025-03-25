<script setup lang="ts">
import CreateSite from '@/components/CreateSite.vue';
import NewEventForm from '@/components/NewEventForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/template/PlaceholderPattern.vue';
import Events from '@/components/Events.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps({
    hasWebsite: Boolean,
    repoUrl: String,
    liveUrl: String,
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-if="!hasWebsite" class="div-pattern flex items-center justify-center">
                    <CreateSite />
                </div>
                <div v-else class="div-pattern flex flex-col items-center justify-center">
                    <h1>Your site is live!</h1>
                    <a :href="liveUrl" target="_blank" class="hover:text-blue-600">{{ liveUrl }}</a>
                </div>

                <div class="div-pattern">
                    <PlaceholderPattern />
                </div>
                <div class="div-pattern">
                    <PlaceholderPattern />
                </div>
            </div>

            <div
                class="relative grid min-h-[100vh] flex-1 auto-rows-min gap-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min md:grid-cols-3"
            >
                <div class="full-length-div-pattern">
                    <NewEventForm />

                    <Events />
                </div>

                <div class="full-length-div-pattern">
                    <PlaceholderPattern />
                </div>
                <div class="full-length-div-pattern">
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.div-pattern {
    @apply relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border;
}

.full-length-div-pattern {
    @apply relative min-h-[100vh] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border;
}
</style>
