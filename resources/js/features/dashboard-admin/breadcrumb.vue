<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    items?: { label: string; href?: string }[];
}>();
</script>

<template>
    <div class="p-4 pb-1">
        <h1 class="text-xl font-bold text-indigo-950">Dashboard</h1>
        <nav class="mt-1 flex text-xs text-neutral-500">
            <Link href="/" class="transition-colors hover:text-indigo-950"> Home </Link>
            <template v-for="(item, index) in items" :key="index">
                <span class="mx-2">/</span>

                <Link v-if="item.href" :href="item.href" class="transition-colors hover:text-indigo-950">
                    {{ item.label }}
                </Link>

                <span v-else>
                    {{ item.label }}
                </span>
            </template>
        </nav>
    </div>
</template>
