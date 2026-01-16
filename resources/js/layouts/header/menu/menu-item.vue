<template>
    <li ref="dropdownRef" class="relative z-50 p-1 text-neutral-700">
        <Link v-if="!hasDropdown" class="flex items-center px-2 py-1.5 hover:rounded-md hover:bg-neutral-100 focus:bg-neutral-100" :href="href">
            <div class="flex items-center">
                <component width="17" height="17" :is="icon" />
                <span class="ml-1.5 text-sm">{{ label }}</span>
            </div>
        </Link>

        <button v-else class="flex cursor-pointer items-center" @click="click">
            <div class="flex items-center">
                <component :is="icon" />
                <span class="ml-1.5 text-sm">{{ label }}</span>
            </div>

            <div v-if="hasDropdown" :class="['ml-4 transition-all', menuOpen ? 'rotate-180' : 'rotate-0']">
                <RiArrowDownSLine />
            </div>
        </button>

        <AnimatePresence>
            <motion.div
                v-if="menuOpen"
                :initial="{ opacity: 0, top: 20 }"
                :animate="{ opacity: 1, top: '40px' }"
                :exit="{ opacity: 0, top: 20 }"
                class="absolute top-[40px] z-[9999] flex max-w-[200px] min-w-[200px] flex-col gap-0 rounded-lg bg-white shadow"
            >
                <slot />
            </motion.div>
        </AnimatePresence>
    </li>
</template>

<script setup lang="ts">
import RiArrowDownSLine from '@/icons/RiArrowDownSLine.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { AnimatePresence, motion } from 'motion-v';
import type { Component } from 'vue';
import { ref, watch } from 'vue';

defineProps<{
    href: string | { url: string; method: any } | any;
    icon?: Component;
    label: string;
    hasDropdown?: boolean;
}>();

const menuOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const click = () => {
    menuOpen.value = !menuOpen.value;
};

// Close dropdown when page changes
const page = usePage();
watch(page, () => {
    menuOpen.value = false;
});

// Close when clicking outside
onClickOutside(dropdownRef, () => {
    menuOpen.value = false;
});
</script>
