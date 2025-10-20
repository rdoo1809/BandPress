<script setup lang="ts">
import CreateSite from '@/components/CreateSite.vue';
import NewEventForm from '@/components/NewEventForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import Events from '@/components/Events.vue';
import Releases from '@/components/Releases.vue';
import NewReleaseForm from '@/components/NewReleaseForm.vue';

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
    dbEvents: Object,
    dbReleases: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Site Status / Create Site Section -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-1 max-w-md">
                <div v-if="!hasWebsite" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex items-center justify-center">
                    <CreateSite />
                </div>
                <div v-else class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex flex-col items-center justify-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 font-saira uppercase tracking-wider">
                        Your site is live at:
                    </h1>
                    <a :href="liveUrl" target="_blank" class="text-blue-600 hover:text-blue-700 underline text-lg mb-4">{{ liveUrl }}</a>

                    <!-- Quick Action Buttons -->
                    <div class="space-y-3">
                        <Link href="/builder" class="block w-full text-center px-4 py-3 bg-figma-red text-white font-bold rounded-lg uppercase tracking-wider font-saira hover:bg-red-700 transition-colors">
                            Open Builder
                        </Link>
                        <button class="block w-full px-4 py-2 bg-gray-600 text-white font-saira uppercase tracking-wider rounded-lg hover:bg-gray-700 transition-colors">
                            View Site
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event Management Section (Keep on Dashboard) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                    <NewEventForm />
                    <Events :database-events="dbEvents"/>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                    <NewReleaseForm />
                    <Releases :database-releases="dbReleases"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

