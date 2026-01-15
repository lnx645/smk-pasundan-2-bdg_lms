<template>
    <div class="mx-auto max-w-full">
        <PageTitle title="Periksa Tugas" subtitle="Daftar siswa yang telah mengumpulkan tugas" />

        <div class="mt-6 mb-4 flex items-center justify-between">
            <div class="relative w-full max-w-xs">
                <span class="absolute top-1/2 left-3 -translate-y-1/2 text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="Cari siswa..."
                    class="w-full rounded-lg border border-neutral-200 py-2 pr-4 pl-9 text-sm transition-all placeholder:text-neutral-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                />
            </div>
            <div class="text-xs font-medium text-neutral-500">
                Total: <span class="text-neutral-900">{{ $page.props.jawaban?.length || 0 }}</span> Siswa
            </div>
        </div>

        <div class="min-h-[400px] overflow-visible rounded-xl border border-neutral-200 bg-white shadow-sm">
            <div
                class="grid grid-cols-12 gap-4 border-b border-neutral-200 bg-neutral-50/80 px-6 py-3 text-xs font-semibold tracking-wider text-neutral-500 uppercase"
            >
                <div class="col-span-5 md:col-span-4">Siswa</div>
                <div class="col-span-3 md:col-span-3">Waktu & File</div>
                <div class="col-span-2 text-center md:col-span-3">Status / Nilai</div>
                <div class="col-span-2 text-right">Aksi</div>
            </div>

            <div class="divide-y divide-neutral-100">
                <div
                    v-for="(item, i) in $page.props.jawaban as any"
                    :key="i"
                    class="group relative grid grid-cols-12 items-center gap-4 px-6 py-4 transition-colors hover:bg-blue-50/30"
                >
                    <div class="col-span-5 flex items-center gap-3 md:col-span-4">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border border-neutral-200 bg-gradient-to-br from-neutral-100 to-neutral-200 text-xs font-bold text-neutral-600"
                        >
                            {{ getInitials(item.user_name) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm leading-tight font-semibold text-neutral-800">
                                {{ item.user_name }}
                            </p>
                            <p class="mt-0.5 truncate text-[11px] text-neutral-500">
                                <span class="font-medium text-neutral-600">{{ item.kelas_nama }}</span> • {{ item.nis }}
                            </p>
                        </div>
                    </div>

                    <div class="col-span-3 flex flex-col justify-center md:col-span-3">
                        <span class="mb-1 flex items-center gap-1 text-xs text-neutral-500">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            {{ formatTanggal(item.created_at) }}
                        </span>

                        <div v-if="item.file_url">
                            <a
                                :href="item.file_url"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 text-[11px] font-medium text-blue-600 decoration-blue-300 underline-offset-2 transition-all hover:text-blue-700 hover:underline"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                    />
                                </svg>
                                Lihat File
                            </a>
                        </div>
                        <div v-else class="text-[11px] text-neutral-400 italic">Tanpa file</div>
                    </div>

                    <div class="col-span-2 flex justify-center md:col-span-3">
                        <div
                            v-if="item.nilai"
                            class="rounded-full border border-green-200 bg-green-100 px-3 py-1 text-xs font-bold text-green-700 shadow-sm"
                        >
                            {{ item.nilai.angka }}
                        </div>
                        <div
                            v-else
                            class="rounded-full border border-neutral-200 bg-neutral-100 px-3 py-1 text-[10px] font-medium tracking-wide text-neutral-500 uppercase"
                        >
                            Belum Dinilai
                        </div>
                    </div>

                    <div class="relative col-span-2 text-right">
                        <div class="relative inline-block" v-click-outside="() => (openIndex = null)">
                            <button
                                @click.stop.prevent="toggle(i as number)"
                                class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 transition-all hover:bg-neutral-100 hover:text-neutral-700 focus:ring-2 focus:ring-neutral-200"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="1" />
                                    <circle cx="19" cy="12" r="1" />
                                    <circle cx="5" cy="12" r="1" />
                                </svg>
                            </button>

                            <Transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <div
                                    v-if="openIndex === i"
                                    class="absolute top-full right-0 z-[50] mt-1 w-48 origin-top-right overflow-hidden rounded-lg border border-neutral-100 bg-white py-1 shadow-xl shadow-neutral-200/50"
                                >
                                    <button
                                        @click="lihat(item)"
                                        class="group flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-xs text-neutral-700 transition hover:bg-blue-50 hover:text-blue-700"
                                    >
                                        <span class="text-neutral-400 group-hover:text-blue-500"
                                            ><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline></svg
                                        ></span>
                                        Detail Jawaban
                                    </button>

                                    <button
                                        @click="nilai(item)"
                                        class="group flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-xs text-neutral-700 transition hover:bg-orange-50 hover:text-orange-700"
                                    >
                                        <span class="text-neutral-400 group-hover:text-orange-500"
                                            ><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polygon
                                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                                                ></polygon></svg
                                        ></span>
                                        Input Nilai
                                    </button>

                                    <div class="my-1 h-px bg-neutral-100"></div>

                                    <button
                                        @click="hapus(item)"
                                        class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-xs text-red-600 transition hover:bg-red-50"
                                    >
                                        <span class="text-red-400"
                                            ><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg
                                        ></span>
                                        Hapus Data
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>

                <div v-if="!$page.props.jawaban || $page.props.jawaban.length === 0" class="py-16 text-center">
                    <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-50">
                        <svg class="h-8 w-8 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </div>
                    <p class="font-medium text-neutral-500">Belum ada tugas dikumpulkan</p>
                    <p class="mt-1 text-xs text-neutral-400">Siswa mungkin belum mengirimkan jawaban.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import PageTitle from '@/layouts/page-title.vue';
import { formatTanggal } from '@/lib/utils';
import { lihat_jawaban } from '@/routes/guru/tugas';
import { JawabanTugas } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const openIndex = ref<number | null>(null);

const toggle = (index: number) => {
    openIndex.value = openIndex.value === index ? null : index;
};

const page = usePage().props as any;

const lihat = (item: JawabanTugas) => {
    router.visit(
        lihat_jawaban({
            id: page.tugas_id,
            jawaban_id: item.jawaban_id,
        }).url,
    );
    openIndex.value = null;
};

const nilai = (item: any) => {
    console.log('Nilai', item);
    openIndex.value = null;
};

const hapus = (item: any) => {
    console.log('Hapus', item);
    openIndex.value = null;
};

// Helper untuk inisial nama
const getInitials = (name: string) => {
    if (!name) return '??';
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
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
