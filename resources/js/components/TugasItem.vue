<script setup lang="ts">
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import { formatTanggal } from '@/lib/utils';
import { computed } from 'vue';

const props = defineProps<{
    item: any;
}>();

// Simulasi progress (bisa diambil dari item.percentage jika ada di database)
// Jika tidak ada, default ke 0 atau angka statis
const progress = props.item.progress || 50; 

// Helper untuk warna deadline (Opsional logic)
const isUrgent = computed(() => {
    const today = new Date();
    const deadline = new Date(props.item.deadline);
    const diffTime = deadline.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
    return diffDays <= 3 && diffDays >= 0;
});
</script>

<template>
    <div class="group relative flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">
        
        <div class="flex items-start gap-4 mb-4">
            <div class="shrink-0 rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 p-3 text-orange-600 ring-1 ring-orange-200/50 group-hover:from-orange-100 group-hover:to-orange-200 transition-colors">
                <HugeiconsTaskDaily01 class="text-2xl" />
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <h1 class="text-base font-bold text-neutral-800 leading-tight group-hover:text-blue-700 transition-colors line-clamp-2">
                        {{ item.title }}
                    </h1>
                    
                    <div :class="[
                        'shrink-0 px-2.5 py-1 rounded-md text-[10px] font-bold border uppercase tracking-wider',
                        isUrgent 
                            ? 'bg-red-50 text-red-600 border-red-100' 
                            : 'bg-neutral-50 text-neutral-500 border-neutral-100'
                    ]">
                        {{ formatTanggal(item.deadline) }}
                    </div>
                </div>
                
                <p class="mt-1 text-xs text-neutral-500 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-300"></span>
                    {{ item.nama_matpel }}
                </p>
            </div>
        </div>

        <div v-if="item.receiver_type === 'siswa_id' && item.receiver_type_id?.length" 
             class="mb-5 bg-neutral-50/50 rounded-lg border border-neutral-100 p-3">
            <p class="text-[10px] font-bold text-neutral-400 uppercase tracking-wide mb-2">Ditugaskan Kepada:</p>
            <div class="flex flex-wrap gap-1.5">
                <div v-for="(siswa, key) in item.receiver_type_id" :key="key" 
                     class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium bg-white border border-neutral-200 text-neutral-600 shadow-sm">
                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-600 text-[9px] flex items-center justify-center mr-1.5 font-bold">
                        {{ siswa.user.name.charAt(0) }}
                    </span>
                    <span class="truncate max-w-[100px]">{{ siswa.user.name }}</span>
                </div>
            </div>
        </div>

        <div class="mt-auto pt-4 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            <div class="flex-1">
                <div class="flex justify-between text-[11px] mb-1.5">
                    <span class="text-neutral-500 font-medium">Pengumpulan</span>
                    <span class="font-bold text-neutral-700">{{ progress }}%</span>
                </div>
                <div class="h-2 w-full bg-neutral-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all duration-500"
                         :style="{ width: progress + '%' }">
                    </div>
                </div>
            </div>

            <div class="sm:pl-4 sm:border-l sm:border-neutral-100 flex items-center justify-end">
                <slot name="buttons" :item="item" />
            </div>
        </div>

    </div>
</template>