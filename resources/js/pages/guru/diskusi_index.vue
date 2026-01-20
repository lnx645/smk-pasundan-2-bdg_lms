<template>
    <Link
        v-for="item in props.kelas"
        :href="
            DiscusionController.index({
                kelas_id: item.id_kelas,
            })
        "
        :key="item.id_kelas"
        class="mx-auto max-w-3xl px-4 py-3"
    >
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                        />
                    </svg>
                </div>

                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">{{ item.nama_kelas }}</h1>
                        <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">ID: {{ item.id_kelas }}</span>
                    </div>
                    <p class="text-xs font-medium text-slate-400">Ruang diskusi mata pelajaran {{ current_matpel_name }}</p>
                </div>
            </div>

            <div class="border-l border-slate-100 px-4 text-right">
                <div class="text-xl leading-none font-black text-indigo-600">{{ item.jumlah_siswa }}</div>
                <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Siswa Terdaftar</div>
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import { Link, usePage } from '@inertiajs/vue3';

// Ambil semua props dari Inertia
const props = usePage().props as any;

const current_matpel_name = props.matpel_name ?? 'Umum';
</script>
