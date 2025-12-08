<script setup lang="ts">
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import MaterialSymbolsCheckCircleUnreadOutline from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import { formatTanggal } from '@/lib/utils';
defineProps<{
    item: any;
}>();
</script>

<template>
    <div
        class="group rounded-xl border border-neutral-200 bg-white p-4 shadow-xs transition-all hover:shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-4">
            <!-- ICON -->
            <div
                class="w-fit rounded-lg bg-neutral-100 p-2 text-neutral-700 transition-colors group-hover:bg-neutral-200">
                <HugeiconsTaskDaily01 class="text-2xl text-orange-700 sm:text-3xl" />
            </div>

            <!-- CONTENT -->
            <div class="flex-1">
                <h1 class="text-sm leading-snug font-semibold text-neutral-800 sm:text-base">
                    {{ item.title }}
                </h1>

                <h2 class="mt-0.5 text-[11px] text-neutral-500 sm:text-xs">Mata Pelajaran: {{ item.nama_matpel }}</h2>

                <div class="mt-2 flex items-center gap-2">
                    <MaterialSymbolsCheckCircleUnreadOutline class="text-sm text-green-600" />
                    <span class="text-xs text-neutral-700">50% Mengumpulkan</span>
                </div>
                <div v-if="item.receiver_type === 'siswa_id'">
                    <span class="text-sm">Reciver:</span>
                    <ul class="text-xs pl-2 text-neutral-500">
                        <li v-for="(i, key) in item.receiver_type_id">
                            {{ key + 1 }}. {{ i.user.name }} - {{ i.kelas.nama }}
                        </li>
                    </ul>
                </div>
                <div class="mt-1 text-[11px] text-neutral-800 sm:text-xs">
                    Deadline:
                    <span class="font-medium">{{ formatTanggal(item.deadline) }}</span>
                </div>
            </div>

            <!-- BUTTON -->
            <div class="mt-3 sm:mt-0 sm:ml-auto">
                <slot name="buttons" :item="item" />
            </div>
        </div>
    </div>
</template>
