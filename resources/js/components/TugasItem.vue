<script setup lang="ts">
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import { cn, formatTanggal } from '@/lib/utils';
import { computed } from 'vue';

const props = defineProps<{
    item: any;
}>();
const progress = props.item.persen_submit;

const isUrgent = computed(() => {
    const today = new Date();
    const deadline = new Date(props.item.deadline);
    const diffTime = deadline.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays <= 3 && diffDays >= 0;
});
</script>

<template>
    <div
        class="group relative flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg"
    >
        <div class="mb-4 flex items-start gap-4">
            <div
                class="shrink-0 rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 p-3 text-orange-600 ring-1 ring-orange-200/50 transition-colors group-hover:from-orange-100 group-hover:to-orange-200"
            >
                <HugeiconsTaskDaily01 class="text-2xl" />
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <h1 class="line-clamp-2 text-base leading-tight font-bold text-neutral-800 transition-colors group-hover:text-blue-700">
                        {{ item.title }}
                    </h1>

                    <div
                        :class="[
                            'shrink-0 rounded-md border px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase',
                            isUrgent ? 'border-red-100 bg-red-50 text-red-600' : 'border-neutral-100 bg-neutral-50 text-neutral-500',
                        ]"
                    >
                        {{ formatTanggal(item.deadline) }}
                    </div>
                </div>

                <p class="mt-1 flex items-center gap-1 text-xs font-medium text-neutral-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-300"></span>
                    {{ item.nama_matpel }}
                </p>
            </div>
        </div>
        <div
            v-if="item.receiver_type === 'siswa_id' && item.receiver_users?.length"
            class="mb-5 rounded-lg border border-neutral-100 bg-neutral-50/50 p-3"
        >
            <p class="mb-2 text-[10px] font-bold tracking-wide text-neutral-400 uppercase">Ditugaskan Kepada:</p>
            <div class="flex flex-wrap gap-1.5">
                <div
                    v-for="(siswa, key) in item.receiver_users"
                    :key="key"
                    :class="
                        cn(
                            'inline-flex items-center rounded border border-neutral-200 bg-white px-2 py-1 text-[11px] font-medium text-neutral-600 shadow-sm',
                            siswa.dikerjakan ? 'border-green-600 bg-green-500 text-white' : 'border-red-600 bg-red-500 text-white',
                        )
                    "
                >
                    <span class="mr-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-blue-100 text-[9px] font-bold text-blue-600">
                        {{ siswa.name.charAt(0) }}
                    </span>
                    <span class="max-w-[100px] truncate">{{ siswa.name }} {{ siswa.id }}</span>
                </div>
            </div>
        </div>
        <div class="mt-auto flex flex-col justify-between gap-4 border-t border-neutral-100 pt-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <div class="mb-1.5 flex justify-between text-[11px]">
                    <span class="font-medium text-neutral-500">Pengumpulan</span>
                    <span class="font-bold text-neutral-700">{{ progress }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-neutral-100">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-green-400 to-emerald-500 transition-all duration-500"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>
            </div>

            <div class="flex items-center justify-end sm:border-l sm:border-neutral-100 sm:pl-4">
                <slot name="buttons" :item="item" />
            </div>
        </div>
    </div>
</template>
