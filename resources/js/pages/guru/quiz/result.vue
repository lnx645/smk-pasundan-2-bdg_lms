<script setup>
import { ArrowDownTrayIcon, ChevronLeftIcon, FunnelIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({
    quiz: Object,
    attempts: Array,
    stats: Object,
    allKelas: Array, // Data kelas dari controller
    filters: Object,
});

// State untuk filter
const search = ref(props.filters.search || '');
const selectedKelas = ref(props.filters.kelas || '');

// Fungsi Reload Data (Digunakan oleh Search & Filter Kelas)
const reloadData = debounce(() => {
    router.get(
        `/guru/quiz/${props.quiz.id}/result`,
        {
            search: search.value,
            kelas: selectedKelas.value, // Kirim parameter kelas
        },
        { preserveState: true, replace: true },
    );
}, 300);

// Watch perubahan Search
watch(search, () => {
    reloadData();
});

// Watch perubahan Dropdown Kelas
watch(selectedKelas, () => {
    reloadData();
});

// Helper Format Tanggal
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Hasil - ${quiz.title}`" />

    <div class="min-h-screen bg-gray-50 px-4 py-8 font-sans text-gray-900 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <Link href="/guru/quiz" class="rounded-full bg-white p-1 text-gray-500 shadow-sm ring-1 ring-gray-200 hover:text-indigo-600">
                            <ChevronLeftIcon class="h-5 w-5" />
                        </Link>
                        <h1 class="text-2xl font-bold text-gray-900">Hasil Kuis</h1>
                    </div>
                    <p class="mt-1 ml-9 text-sm text-gray-500">{{ quiz.title }}</p>
                </div>

                <div class="flex gap-3">
                    <button
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50"
                    >
                        <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" />
                        Export Excel
                    </button>
                </div>
            </div>

            <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <dt class="truncate text-sm font-medium text-gray-500">Rata-rata Nilai</dt>
                    <dd class="mt-2 flex items-baseline">
                        <span class="text-3xl font-bold tracking-tight text-gray-900">
                            {{ stats.average ? Number(stats.average).toFixed(1) : '0' }}
                        </span>
                    </dd>
                </div>
                <div class="overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <dt class="truncate text-sm font-medium text-gray-500">Nilai Tertinggi</dt>
                    <dd class="mt-2 flex items-baseline">
                        <span class="text-3xl font-bold tracking-tight text-emerald-600">
                            {{ stats.max || 0 }}
                        </span>
                    </dd>
                </div>
                <div class="overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <dt class="truncate text-sm font-medium text-gray-500">Nilai Terendah</dt>
                    <dd class="mt-2 flex items-baseline">
                        <span class="text-3xl font-bold tracking-tight text-rose-600">
                            {{ stats.min || 0 }}
                        </span>
                    </dd>
                </div>
                <div class="overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                    <dt class="truncate text-sm font-medium text-gray-500">Kelulusan (KKM: {{ quiz.passing_grade }})</dt>
                    <dd class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-bold tracking-tight text-indigo-600">
                            {{ stats.passed }}
                        </span>
                        <span class="text-sm text-gray-500">/ {{ stats.total_students }} Siswa</span>
                    </dd>
                </div>
            </div>

            <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Cari nama siswa..."
                    />
                </div>

                <div class="relative min-w-[200px]">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <FunnelIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <select
                        v-model="selectedKelas"
                        class="block w-full rounded-lg border-0 py-2.5 pl-10 ring-1 ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="k in allKelas" :key="k.id" :value="k.id">
                            {{ k.nama }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-xs font-bold tracking-wide text-gray-500 uppercase sm:pl-6">
                                    Siswa
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold tracking-wide text-gray-500 uppercase">Kelas</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold tracking-wide text-gray-500 uppercase">
                                    Waktu Submit
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold tracking-wide text-gray-500 uppercase">Nilai</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-xs font-bold tracking-wide text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-if="attempts.length === 0">
                                <td colspan="5" class="py-10 text-center text-sm text-gray-500">
                                    {{ selectedKelas ? 'Tidak ada data nilai untuk kelas ini.' : 'Belum ada data nilai yang masuk.' }}
                                </td>
                            </tr>

                            <tr v-for="attempt in attempts" :key="attempt.id" class="hover:bg-gray-50">
                                <td class="py-4 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ attempt.user?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ attempt.user?.email }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
                                    {{ attempt.user?.siswa?.kelas?.nama || '-' }}
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500">
                                    {{ formatDate(attempt.finished_at) }}
                                </td>
                                <td class="px-3 py-4 text-center text-sm font-bold whitespace-nowrap text-gray-900">
                                    {{ attempt.score }}
                                </td>
                                <td class="px-3 py-4 text-center text-sm whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="
                                            attempt.score >= quiz.passing_grade
                                                ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                                                : 'bg-red-50 text-red-700 ring-1 ring-red-600/20'
                                        "
                                    >
                                        {{ attempt.score >= quiz.passing_grade ? 'Lulus' : 'Remedial' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
