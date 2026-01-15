<script setup>
import PageTitle from '@/layouts/page-title.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    daftarNilai: Array,
});

// Helper format tanggal
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

// Helper warna status/nilai
const getScoreColor = (score) => {
    return score >= 75 ? 'text-green-600 bg-green-50 border-green-100' : 'text-red-600 bg-red-50 border-red-100';
};
</script>

<template>
    <Head title="Daftar Nilai" />

    <div class="py-3">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <PageTitle :title="`Transkrip Nilai`" subtitle="Riwayat hasil penilaian tugas dan ujian." />

            <div v-if="daftarNilai.length === 0" class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada nilai</h3>
                <p class="mt-1 text-sm text-gray-500">Tugas yang kamu kerjakan belum dinilai oleh guru.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="nilai in daftarNilai"
                    :key="nilai.id"
                    class="group relative flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div
                            class="flex items-center gap-1 rounded border border-gray-200 bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-3.5 w-3.5 text-gray-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                />
                            </svg>
                            <span class="max-w-[150px] truncate">{{ nilai.tugas?.mapel?.nama || 'Umum' }}</span>
                        </div>

                        <span
                            :class="[
                                'rounded border px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase',
                                nilai.angka >= 75 ? 'border-green-200 bg-green-100 text-green-700' : 'border-red-200 bg-red-100 text-red-700',
                            ]"
                        >
                            {{ nilai.angka >= 75 ? 'Lulus' : 'Remedial' }}
                        </span>
                    </div>

                    <div class="mb-6">
                        <h3 class="line-clamp-2 text-lg leading-tight font-bold text-gray-800 transition-colors group-hover:text-blue-600">
                            {{ nilai.tugas?.judul }}
                        </h3>

                        <div class="mt-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <p class="mb-1 text-[10px] font-bold text-gray-400 uppercase">Catatan Guru:</p>
                            <p class="line-clamp-3 text-sm text-gray-600 italic">"{{ nilai.komentar || 'Tidak ada catatan tambahan.' }}"</p>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-800 text-xs font-bold text-white shadow-sm">
                                    {{ nilai.tugas?.guru?.nama ? nilai.tugas.guru.nama.substring(0, 2).toUpperCase() : 'GU' }}
                                </div>
                                <div>
                                    <p class="line-clamp-1 w-24 text-xs font-bold text-gray-900">
                                        {{ nilai.tugas?.guru?.nama || 'Guru' }}
                                    </p>
                                    <p class="text-[10px] text-gray-500">Pengajar</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">DINILAI PADA</p>
                                <p class="text-xs font-semibold text-gray-600">
                                    {{ formatDate(nilai.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div :class="['flex w-full items-center justify-between rounded-lg border px-4 py-3', getScoreColor(nilai.angka)]">
                            <span class="text-xs font-bold tracking-wider uppercase opacity-80">Nilai Akhir</span>
                            <span class="text-2xl font-extrabold">{{ nilai.angka }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
