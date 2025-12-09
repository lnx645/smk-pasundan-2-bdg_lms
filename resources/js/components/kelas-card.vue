<script setup lang="ts">
import ClarityUsersSolidBadged from '@/icons/ClarityUsersSolidBadged.vue';

// Definisi Props dengan Default Value
withDefaults(defineProps<{
    nama_kelas: string;
    jumlah_siswa?: number;
    subText?: string; // Opsional: misal "Wali Kelas: Budi"
}>(), {
    nama_kelas: 'Tanpa Nama Kelas',
    jumlah_siswa: 0,
});
</script>

<template>
    <button
        class="group relative flex w-full flex-col items-start justify-between rounded-xl border border-neutral-200 bg-white p-5 text-left shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20">
        <div
            class="absolute right-0 top-0 h-20 w-20 translate-x-1/3 -translate-y-1/3 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 transition-opacity group-hover:opacity-100 blur-2xl">
        </div>

        <div class="relative z-10 w-full">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h3
                        class="text-lg font-bold text-neutral-800 leading-tight group-hover:text-blue-700 transition-colors line-clamp-2">
                        {{ nama_kelas }}
                    </h3>
                    <p v-if="subText" class="mt-1 text-xs text-neutral-400 font-medium">
                        {{ subText }}
                    </p>
                </div>

                <div
                    class="shrink-0 rounded-lg bg-neutral-50 p-2 text-neutral-400 transition-colors group-hover:bg-blue-50 group-hover:text-blue-600">
                    <slot name="icon">
                        <ClarityUsersSolidBadged class="text-xl" />
                    </slot>
                </div>
            </div>

            <div class="my-4 h-px w-full bg-neutral-100"></div>

            <div class="flex items-center justify-between w-full">
                <div
                    class="flex items-center gap-1.5 rounded-full bg-neutral-50 px-2.5 py-1 text-xs font-medium text-neutral-600 border border-neutral-100 group-hover:border-blue-100 group-hover:bg-blue-50/50 group-hover:text-blue-700 transition-colors">
                    <ClarityUsersSolidBadged class="text-[14px]" />
                    <span>{{ jumlah_siswa }} Siswa</span>
                </div>

                <div class="text-neutral-300 group-hover:text-blue-500 transition-colors">
                    <slot />
                </div>
            </div>
        </div>
    </button>
</template>