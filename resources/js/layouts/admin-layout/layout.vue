<script setup lang="ts">
import { useAdminSidebarStore } from '@/features/store/admin-sidebar.store';
import { onClickOutside, useMediaQuery } from '@vueuse/core';
import { onMounted, useTemplateRef, watch } from 'vue';
import Base from '../base.vue';
import Header from './header.vue';
import Sidebar from './sidebar.vue';
const store = useAdminSidebarStore();
const sidebarRef = useTemplateRef('sidebarRef');
const mk = useMediaQuery('(min-width: 1024px)');

onClickOutside(sidebarRef, (e) => {
    if (store.isOpen && !mk.value) {
        store.setOpen(false);
    }
},{
    ignore : ['#sidebar-trigger']
});

</script>

<template>
    <Base>
        <Header />
        <div class="flex max-w-full">
            <Transition
                enter-active-class="transition-all duration-300 ease-in-out"
                leave-active-class="transition-all duration-300 ease-in-out"
                enter-from-class="-ml-64 opacity-0"
                enter-to-class="ml-0 opacity-100"
                leave-from-class="ml-0 opacity-100"
                leave-to-class="-ml-64 opacity-0"
            >
                <div ref="sidebarRef" v-show="store.isOpen" class="max-w-64 min-w-64 lg:block">
                    <Sidebar /></div
            ></Transition>

            <div class="container mx-auto mb-4 max-w-full flex-1 px-2 lg:px-0">
                <slot />
            </div>
        </div>
    </Base>
</template>
<style>
body {
    background: white;
}
</style>
