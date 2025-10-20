<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SitePreview from '@/components/SitePreview.vue';
import SiteConfiguration from '@/components/SiteConfiguration.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Builder',
        href: '/builder',
    },
];

defineProps<{
    hasWebsite: boolean;
    liveUrl?: string;
    contents?: object;
}>();
</script>

<template>
    <Head title="Builder" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Site Status Section -->
            <div v-if="!hasWebsite" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md flex items-center justify-center">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-saira uppercase tracking-wider">
                        No Website Found
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Create a website first from the dashboard to access the builder.
                    </p>
                </div>
            </div>

            <!-- Main Builder Interface -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1">
                <div class="h-full">
                    <SitePreview :live-url="liveUrl" />
                </div>

                <div class="h-full">
                    <SiteConfiguration />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
