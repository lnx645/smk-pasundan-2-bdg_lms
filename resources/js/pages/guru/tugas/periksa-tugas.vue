<template>
    <div class="max-w-full mx-auto">
        <PageTitle title="Periksa Tugas" subtitle="Daftar siswa yang telah mengumpulkan tugas" />

        <div class="flex items-center justify-between mb-4 mt-6">
            <div class="relative w-full max-w-xs">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" placeholder="Cari siswa..." 
                    class="w-full pl-9 pr-4 py-2 text-sm border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-neutral-400">
            </div>
            <div class="text-xs text-neutral-500 font-medium">
                Total: <span class="text-neutral-900">{{ $page.props.jawaban?.length || 0 }}</span> Siswa
            </div>
        </div>

        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-visible min-h-[400px]">
            
            <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-neutral-50/80 border-b border-neutral-200 text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                <div class="col-span-5 md:col-span-4">Siswa</div>
                <div class="col-span-3 md:col-span-3">Waktu & File</div>
                <div class="col-span-2 md:col-span-3 text-center">Status / Nilai</div>
                <div class="col-span-2 text-right">Aksi</div>
            </div>

            <div class="divide-y divide-neutral-100">
                <div v-for="(item, i) in $page.props.jawaban" :key="i" 
                    class="group grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-blue-50/30 transition-colors relative">
                    
                    <div class="col-span-5 md:col-span-4 flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 text-neutral-600 flex items-center justify-center font-bold text-xs border border-neutral-200">
                            {{ getInitials(item.user_name) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-neutral-800 truncate leading-tight">
                                {{ item.user_name }}
                            </p>
                            <p class="text-[11px] text-neutral-500 mt-0.5 truncate">
                                <span class="font-medium text-neutral-600">{{ item.kelas_nama }}</span> • {{ item.nis }}
                            </p>
                        </div>
                    </div>

                    <div class="col-span-3 md:col-span-3 flex flex-col justify-center">
                        <span class="text-xs text-neutral-500 mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ formatTanggal(item.created_at) }}
                        </span>
                        
                        <div v-if="item.file_url">
                            <a :href="item.file_url" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-blue-600 hover:text-blue-700 hover:underline decoration-blue-300 underline-offset-2 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                Lihat File
                            </a>
                        </div>
                        <div v-else class="text-[11px] text-neutral-400 italic">Tanpa file</div>
                    </div>

                    <div class="col-span-2 md:col-span-3 flex justify-center">
                        <div v-if="item.nilai || item.nilai === 0" 
                             class="px-3 py-1 rounded-full bg-green-100 border border-green-200 text-green-700 text-xs font-bold shadow-sm">
                            {{ item.nilai }}
                        </div>
                        <div v-else 
                             class="px-3 py-1 rounded-full bg-neutral-100 border border-neutral-200 text-neutral-500 text-[10px] font-medium uppercase tracking-wide">
                            Belum Dinilai
                        </div>
                    </div>

                    <div class="col-span-2 text-right relative">
                         <div class="relative inline-block" v-click-outside="() => openIndex = null">
                            <button @click.stop.prevent="toggle(i)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-neutral-100 text-neutral-400 hover:text-neutral-700 transition-all ml-auto focus:ring-2 focus:ring-neutral-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                            </button>

                            <Transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div v-if="openIndex === i"
                                    class="absolute right-0 top-full mt-1 w-48 bg-white shadow-xl shadow-neutral-200/50 rounded-lg border border-neutral-100 z-[50] overflow-hidden origin-top-right py-1">
                                    
                                    <button @click="lihat(item)" class="w-full text-left px-4 py-2.5 text-xs text-neutral-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2.5 group">
                                        <span class="text-neutral-400 group-hover:text-blue-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></span>
                                        Detail Jawaban
                                    </button>

                                    <button @click="nilai(item)" class="w-full text-left px-4 py-2.5 text-xs text-neutral-700 hover:bg-orange-50 hover:text-orange-700 transition flex items-center gap-2.5 group">
                                        <span class="text-neutral-400 group-hover:text-orange-500"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>
                                        Input Nilai
                                    </button>
                                    
                                    <div class="h-px bg-neutral-100 my-1"></div>

                                    <button @click="hapus(item)" class="w-full text-left px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition flex items-center gap-2.5">
                                        <span class="text-red-400"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span>
                                        Hapus Data
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>

                <div v-if="!$page.props.jawaban || $page.props.jawaban.length === 0" class="py-16 text-center">
                    <div class="w-16 h-16 bg-neutral-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <p class="text-neutral-500 font-medium">Belum ada tugas dikumpulkan</p>
                    <p class="text-neutral-400 text-xs mt-1">Siswa mungkin belum mengirimkan jawaban.</p>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import PageTitle from '@/layouts/page-title.vue';
import { formatTanggal } from '@/lib/utils';
import { lihat_jawaban } from '@/routes/guru/tugas';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { JawabanTugas } from '@/types';

const openIndex = ref<number | null>(null);

const toggle = (index: number) => {
    openIndex.value = openIndex.value === index ? null : index;
};

const page = usePage().props as any;

const lihat = (item: JawabanTugas) => {
    router.visit(lihat_jawaban({
        id: page.tugas_id,
        jawaban_id: item.jawaban_id
    }).url);
    openIndex.value = null;
};

const nilai = (item: any) => {
    console.log("Nilai", item);
    openIndex.value = null;
};

const hapus = (item: any) => {
    console.log("Hapus", item);
    openIndex.value = null;
};

// Helper untuk inisial nama
const getInitials = (name: string) => {
    if(!name) return '??';
    return name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase();
};

const vClickOutside = {
    mounted(el: any, binding: any) {
        el.clickOutsideEvent = (event: Event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el: any) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    },
};
</script>