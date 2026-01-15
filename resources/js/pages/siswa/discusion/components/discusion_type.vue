<template>
    <div v-if="type == 'materi'" class="flex items-center justify-between rounded-lg border border-slate-100 bg-white p-4">
        <div class="flex items-center gap-4 overflow-hidden">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-notebook-text"
                >
                    <path d="M2 6h4" />
                    <path d="M2 10h4" />
                    <path d="M2 14h4" />
                    <path d="M2 18h4" />
                    <rect width="16" height="20" x="4" y="2" rx="2" />
                    <path d="M9.5 8h5" />
                    <path d="M9.5 12h5" />
                    <path d="M9.5 16h5" />
                </svg>
            </div>

            <div class="min-w-0">
                <h4 class="truncate text-sm font-bold text-slate-900">
                    {{ item.judul || item.nama || 'Materi Baru' }}
                </h4>
                <p class="truncate text-xs font-medium text-indigo-900/80">Klik tombol detail untuk membuka materi</p>
            </div>
        </div>

        <div class="shrink-0 pl-4">
            <Link
                :href="view({ id: item.id }).url"
                class="inline-flex items-center justify-center rounded-lg bg-orange-400 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-orange-500"
            >
                Lihat Detail
            </Link>
        </div>
    </div>
    <div v-else-if="type == 'tugas'" class="rounded-lg border border-slate-100 bg-white p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 overflow-hidden">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg">
                    <img src="https://edlink.id/assets/img/v1/icon/icon_%20tugas.svg" alt="" />
                </div>

                <div class="min-w-0">
                    <h4 class="truncate text-sm font-bold text-slate-900">
                        {{ item?.title }}
                    </h4>
                    <p class="truncate text-xs font-medium text-indigo-900/80">
                        Batas tanggal & waktu pengumpulan: <span class="font-semibold">{{ item.deadline }}</span>
                    </p>
                </div>
            </div>

            <div class="shrink-0 pl-4">
                <Link
                    :href="TugasSiswaController.kerjakan({ id: item?.tugasID }).url"
                    class="inline-flex items-center justify-center rounded-lg bg-orange-400 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-orange-500"
                >
                    Lihat Detail
                </Link>
            </div>
        </div>
        <div class="mt-3 line-clamp-1 text-sm" v-html="item.content" />
    </div>
</template>
<script setup lang="ts">
import { view } from '@/actions/App/Http/Controllers/MateriController';
import TugasSiswaController from '@/actions/App/Http/Controllers/TugasSiswaController';

defineProps<{
    item: any;
    type: 'tugas' | 'materi';
}>();
</script>
