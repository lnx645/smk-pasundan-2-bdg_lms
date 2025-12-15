<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    data_siswa: Array,
    tanggal_cetak: String,
});

// Opsi: Otomatis print saat halaman dimuat
// onMounted(() => {
//     window.print();
// });

const handlePrint = () => {
    window.print();
};
</script>

<template>
    <Head title="Cetak Laporan Siswa" />

    <div class="min-h-screen bg-gray-100 p-4 print:bg-white print:p-0">
        <div class="no-print mx-auto mb-4 flex max-w-4xl justify-end">
            <button
                @click="handlePrint"
                class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 font-bold text-white transition hover:bg-blue-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                    />
                </svg>
                Cetak Laporan
            </button>
        </div>

        <div class="mx-auto max-w-4xl bg-white p-8 shadow-lg print:w-full print:max-w-none print:p-0 print:shadow-none">
            <div class="mb-6 border-b-2 border-gray-800 pb-4 text-center">
                <h1 class="text-2xl font-bold uppercase">SMK IFSU BANDUNG</h1>
                <p class="text-sm text-gray-600">Jalan Menuju Koding No. 123, Telp (022) 123456</p>
                <h2 class="mt-4 text-xl font-semibold">LAPORAN DATA SISWA</h2>
            </div>

            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                    <tr class="bg-gray-100 print:bg-gray-200">
                        <th class="w-12 border border-gray-300 px-3 py-2 text-center">No</th>
                        <th class="w-24 border border-gray-300 px-3 py-2 text-center">NIS</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Nama Lengkap</th>
                        <th class="w-20 border border-gray-300 px-3 py-2 text-center">L/P</th>
                        <th class="w-32 border border-gray-300 px-3 py-2 text-center">Kelas</th>
                        <th class="w-24 border border-gray-300 px-3 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(siswa, index) in data_siswa" :key="siswa.nis">
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ index + 1 }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ siswa.nis }}</td>
                        <td class="border border-gray-300 px-3 py-2 font-medium">{{ siswa.user?.nama }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ siswa.jenis_kelamin }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">{{ siswa.kelas?.nama ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-center">
                            <span :class="{ 'font-bold text-red-600': siswa.status !== 'aktif' }">
                                {{ siswa.status }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="data_siswa.length === 0">
                        <td colspan="6" class="border border-gray-300 px-3 py-8 text-center text-gray-500">Tidak ada data siswa ditemukan.</td>
                    </tr>
                </tbody>
            </table>

            <div class="page-break-inside-avoid mt-12 flex justify-end">
                <div class="w-64 text-center">
                    <p class="mb-20">Bandung, {{ tanggal_cetak }}</p>
                    <p class="font-bold underline">Kepala Sekolah</p>
                    <p>NIP. .......................</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CSS Khusus Print */
@media print {
    /* Sembunyikan elemen dengan class no-print */
    .no-print {
        display: none !important;
    }

    aside {
        display: none;
    }
    /* Reset background dan shadow agar hemat tinta dan bersih */
    body {
        background: white;
    }

    /* Pastikan tabel tercetak dengan border jelas */
    table,
    th,
    td {
        border: 1px solid black !important;
    }

    /* Mencegah baris tabel terpotong di tengah halaman */
    tr {
        page-break-inside: avoid;
    }

    /* Mengatur margin kertas */
    @page {
        margin: 1.5cm;
        size: A4;
    }
}
</style>
