<script setup lang="ts">
import AppContent from '@/components/template/AppContent.vue';
import AppShell from '@/components/template/AppShell.vue';
import AppSidebar from '@/components/template/AppSidebar.vue';
import AppSidebarHeader from '@/components/template/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
