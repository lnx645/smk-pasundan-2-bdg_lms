<script setup lang="ts">
import NotFoundVector from '@/icons/NotFoundVector.vue'; // Pastikan path icon benar
import { useSlots } from 'vue';

const slots = useSlots();
</script>

<template>
    <div
        class="flex min-h-[400px] w-full flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-200 bg-neutral-50/50 p-10 text-center transition-all hover:bg-neutral-50"
    >
        <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm border border-neutral-100 p-4">
            <NotFoundVector class="h-full w-full opacity-60" />
        </div>

        <h3 class="text-lg font-bold text-neutral-800">
            <slot name="title">Data Tidak Ditemukan</slot>
        </h3>
        
        <p class="mt-2 max-w-md text-sm leading-relaxed text-neutral-500">
            <slot name="description">
                Data yang Anda cari tidak tersedia.
            </slot>
        </p>

        <div v-if="slots.actions" class="mt-6">
             <slot name="actions" />
        </div>
    </div>
</template>