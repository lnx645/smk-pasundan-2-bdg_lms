<script setup>
import { cetakGuru, cetakKelas, cetakLaporanSiswa, cetakNilai } from '@/actions/App/Http/Controllers/Admin/CetakLaporanController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { ref } from 'vue';
// --- PROPS ---
const props = defineProps({
    list_kelas: Array,
    list_matpel: Array,
});

const breadcrumbs = [{ label: 'Dashboard' }, { label: 'Cetak Laporan' }];

// --- STATE FILTER ---
// Default 'all' untuk filter umum, kosong '' untuk yang wajib pilih dulu
const filterSiswa = ref({ kelas_id: 'all', status: 'aktif' });
const filterGuru = ref({ status: 'all' });
const filterKelas = ref({ tingkat: 'all' });
const filterNilai = ref({ kelas_id: '', matpel_kode: '' });

// --- BASE URL KONFIGURASI ---
// Sesuaikan path ini dengan Route di web.php Laravel Anda
const routes = {
    siswa: cetakLaporanSiswa().url, // Sesuaikan URL
    guru: cetakGuru().url, // Sesuaikan URL
    kelas: cetakKelas().url, // Sesuaikan URL
    nilai: cetakNilai().url, // Sesuaikan URL
};

// --- FUNGSI CETAK (Universal) ---
const printLaporan = (type, params) => {
    // Bersihkan parameter yang kosong/null agar URL bersih
    const cleanParams = Object.fromEntries(Object.entries(params).filter(([_, v]) => v != null && v !== ''));

    const queryString = new URLSearchParams(cleanParams).toString();
    const url = `${routes[type]}?${queryString}`;

    window.open(url, '_blank');
};
</script>

<template>
    <Breadcrumb :items="breadcrumbs" />
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-4">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">Pusat Laporan</h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="rounded-full bg-blue-100 p-3 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Data Siswa</h3>
                    </div>

                    <div class="mb-4 flex-1 space-y-3">
                        <label class="block text-xs font-medium text-gray-500">Filter Kelas</label>
                        <select
                            v-model="filterSiswa.kelas_id"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">-- Semua Kelas --</option>
                            <option v-for="k in list_kelas" :key="k.id" :value="k.id">{{ k.nama }}</option>
                        </select>

                        <label class="block text-xs font-medium text-gray-500">Status</label>
                        <select
                            v-model="filterSiswa.status"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">Semua</option>
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>

                    <button @click="printLaporan('siswa', filterSiswa)" class="btn-cetak mt-auto bg-blue-600 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            />
                        </svg>
                        Cetak PDF
                    </button>
                </div>

                <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="rounded-full bg-green-100 p-3 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Data Guru</h3>
                    </div>

                    <div class="mb-4 flex-1 space-y-3">
                        <label class="block text-xs font-medium text-gray-500">Filter Status</label>
                        <select
                            v-model="filterGuru.status"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-green-500 focus:ring-green-500"
                        >
                            <option value="all">Semua Guru</option>
                            <option value="aktif">Hanya Aktif</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-400 italic">Daftar tenaga pengajar beserta spesialisasi.</p>
                    </div>

                    <button @click="printLaporan('guru', filterGuru)" class="btn-cetak mt-auto bg-green-600 hover:bg-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            />
                        </svg>
                        Cetak PDF
                    </button>
                </div>

                <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="rounded-full bg-purple-100 p-3 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Data Kelas</h3>
                    </div>

                    <div class="mb-4 flex-1 space-y-3">
                        <label class="block text-xs font-medium text-gray-500">Filter Tingkat</label>
                        <select
                            v-model="filterKelas.tingkat"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500"
                        >
                            <option value="all">Semua Tingkat</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-400 italic">Rekapitulasi jumlah siswa & wali kelas.</p>
                    </div>

                    <button @click="printLaporan('kelas', filterKelas)" class="btn-cetak mt-auto bg-purple-600 hover:bg-purple-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            />
                        </svg>
                        Cetak PDF
                    </button>
                </div>

                <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="rounded-full bg-orange-100 p-3 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Transkrip Nilai</h3>
                    </div>

                    <div class="mb-4 flex-1 space-y-3">
                        <label class="block text-xs font-medium text-gray-500">Pilih Kelas</label>
                        <select
                            v-model="filterNilai.kelas_id"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500"
                        >
                            <option value="" disabled>-- Pilih Kelas --</option>
                            <option v-for="k in list_kelas" :key="k.id" :value="k.id">{{ k.nama }}</option>
                        </select>

                        <label class="block text-xs font-medium text-gray-500">Pilih Mapel</label>
                        <select
                            v-model="filterNilai.matpel_kode"
                            class="form-select w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500"
                        >
                            <option value="" disabled>-- Pilih Mapel --</option>
                            <option v-for="m in list_matpel" :key="m.id" :value="m.kode">{{ m.nama }}</option>
                        </select>
                    </div>

                    <button
                        @click="printLaporan('nilai', filterNilai)"
                        :disabled="!filterNilai.kelas_id || !filterNilai.matpel_kode"
                        class="mt-auto flex w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                            />
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference "tailwindcss";
/* Utility class agar kodingan template tidak terlalu panjang */
.btn-cetak {
    @apply flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition;
}
</style>
