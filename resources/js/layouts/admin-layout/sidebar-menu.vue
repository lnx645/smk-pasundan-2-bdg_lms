<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

// Menerima props agar bisa dinamis
defineProps<{
    href: string;
    active?: boolean;
}>();
</script>

<template>
    <Link 
        :href="href"
        class="group relative flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-300 ease-in-out"
        :class="[
            active 
                ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5' 
                : 'text-neutral-500 hover:bg-white hover:text-neutral-900 hover:shadow-sm hover:translate-x-1'
        ]"
    >
        <span 
            v-if="active" 
            class="absolute left-0 h-1/2 w-1 rounded-r-full bg-indigo-600 opacity-100 transition-opacity"
        ></span>

        <span class="transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6 text-lg shrink-0">
            <slot name="icon" />
        </span>

        <span class="truncate">
            <slot />
        </span>
    </Link>
</template>