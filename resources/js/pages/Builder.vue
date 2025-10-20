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
</script>

<template>
    <Head title="Builder" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex h-full items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Builder Coming Soon</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        The website builder functionality is under development.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
