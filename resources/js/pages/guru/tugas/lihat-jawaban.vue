<template>
    <div class="max-w-full mt-4 mx-auto space-y-4">
        
        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                    {{ getInitials(jawaban.user_name) }}
                </div>
                <div>
                    <h1 class="font-bold text-neutral-800 text-sm md:text-base leading-tight">
                        {{ jawaban.user_name }}
                        <span class="text-neutral-400 font-normal">({{ jawaban.nis }})</span>
                    </h1>
                    <div class="flex items-center gap-2 text-xs text-neutral-500 mt-0.5">
                        <span class="bg-neutral-100 px-2 py-0.5 rounded text-neutral-600 font-medium border border-neutral-200">
                            {{ jawaban.kelas_nama }}
                        </span>
                        <span>&bull;</span>
                        <span>{{ jawaban.nama_matpel }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-end gap-1">
                <button 
                    @click="router.visit('/guru/tugas/' + page.tugas_id + '/periksa')"
                    class="text-xs font-medium text-neutral-500 hover:text-neutral-900 flex items-center gap-1 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Kembali
                </button>
                <div class="text-xs text-neutral-400">
                    Dikirim: {{ formatTanggal(jawaban.created_at) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div class="md:col-span-2 bg-white rounded-xl border border-neutral-200 shadow-sm flex flex-col min-h-[300px]">
                <div class="px-5 py-3 border-b border-neutral-100 bg-neutral-50/50 rounded-t-xl flex justify-between items-center">
                    <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Jawaban Siswa</h3>
                    <div class="flex gap-2">
                        <button class="p-1 hover:bg-neutral-200 rounded text-neutral-400 hover:text-neutral-600 transition" title="Salin Teks">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-5 flex-grow">
                    <div v-if="jawaban.jawaban" class="prose prose-sm prose-neutral max-w-none text-neutral-700 leading-relaxed text-[14px]">
                        {{ jawaban.jawaban }}
                    </div>
                    <div v-else class="h-full flex flex-col items-center justify-center text-neutral-400 text-sm italic py-10">
                        <span>Tidak ada jawaban tertulis</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                
                <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-2 bg-neutral-50 border-b border-neutral-100 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                        Lampiran File
                    </div>
                    
                    <div class="p-4">
                        <div v-if="jawaban.file_url">
                            <a :href="jawaban.file_url" target="_blank" class="group block relative">
                                <div class="flex items-center gap-3 p-3 rounded-lg border border-neutral-200 bg-white hover:border-blue-400 hover:shadow-sm transition-all">
                                    <div class="bg-blue-50 text-blue-600 p-2 rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-medium text-neutral-700 truncate group-hover:text-blue-600 transition-colors">Download File</p>
                                        <p class="text-[10px] text-neutral-400 uppercase">Klik untuk buka</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div v-else class="text-center py-6">
                            <p class="text-xs text-neutral-400 italic">Tidak ada file dilampirkan</p>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-xl border border-indigo-100 p-4 text-center">
                    <p class="text-xs text-indigo-600 font-medium mb-2">Perlu memberi nilai?</p>
                    <button class="w-full py-2 bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-600 hover:text-white hover:border-transparent text-xs font-semibold rounded-lg transition-all shadow-sm">
                        Buka Form Penilaian
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { formatTanggal } from '@/lib/utils';

const page = usePage().props as any;
const jawaban = page.jawaban;

const getInitials = (name: string) => {
    if (!name) return 'S';
    return name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase();
};
</script>