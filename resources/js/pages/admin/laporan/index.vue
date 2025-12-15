<script setup>
import { cetakGuru, cetakKelas, cetakLaporanSiswa, cetakNilai } from '@/actions/App/Http/Controllers/Admin/CetakLaporanController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
const breadcrumbs = [
    { label: 'Dashboard' },
    {
        label: 'Cetak Laporan',
    },
];
</script>

<template>
    <Breadcrumb :items="breadcrumbs" />
    <div class="py-5">
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-6 px-4 md:grid-cols-2 lg:grid-cols-4">
                <a :href="cetakLaporanSiswa().url" target="_blank" class="group block">
                    <div
                        class="flex h-full flex-col items-center overflow-hidden border border-gray-200 bg-white p-6 text-center shadow-sm transition duration-200 hover:border-blue-500 hover:shadow-md sm:rounded-lg"
                    >
                        <div class="mb-4 rounded-full bg-blue-100 p-4 transition group-hover:bg-blue-200">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Data Siswa</h3>
                        <p class="mb-4 text-sm text-gray-500">Cetak daftar seluruh siswa aktif beserta data kelas.</p>
                        <span class="mt-auto text-sm font-semibold text-blue-600 group-hover:underline">Cetak Sekarang &rarr;</span>
                    </div>
                </a>

                <a :href="cetakGuru().url" target="_blank" class="group block">
                    <div
                        class="flex h-full flex-col items-center overflow-hidden border border-gray-200 bg-white p-6 text-center shadow-sm transition duration-200 hover:border-green-500 hover:shadow-md sm:rounded-lg"
                    >
                        <div class="mb-4 rounded-full bg-green-100 p-4 transition group-hover:bg-green-200">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-green-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Data Guru</h3>
                        <p class="mb-4 text-sm text-gray-500">Laporan data tenaga pengajar dan staf.</p>
                        <span class="mt-auto text-sm font-semibold text-green-600 group-hover:underline">Cetak Sekarang &rarr;</span>
                    </div>
                </a>

                <a :href="cetakKelas().url" target="_blank" class="group block">
                    <div
                        class="flex h-full flex-col items-center overflow-hidden border border-gray-200 bg-white p-6 text-center shadow-sm transition duration-200 hover:border-purple-500 hover:shadow-md sm:rounded-lg"
                    >
                        <div class="mb-4 rounded-full bg-purple-100 p-4 transition group-hover:bg-purple-200">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Data Kelas</h3>
                        <p class="mb-4 text-sm text-gray-500">Rekapitulasi jumlah kelas dan wali kelas.</p>
                        <span class="mt-auto text-sm font-semibold text-purple-600 group-hover:underline">Cetak Sekarang &rarr;</span>
                    </div>
                </a>

                <a :href="cetakNilai().url" target="_blank" class="group block">
                    <div
                        class="flex h-full flex-col items-center overflow-hidden border border-gray-200 bg-white p-6 text-center shadow-sm transition duration-200 hover:border-orange-500 hover:shadow-md sm:rounded-lg"
                    >
                        <div class="mb-4 rounded-full bg-orange-100 p-4 transition group-hover:bg-orange-200">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-orange-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-800">Transkrip Nilai</h3>
                        <p class="mb-4 text-sm text-gray-500">Cetak rekap nilai akademik siswa.</p>
                        <span class="mt-auto text-sm font-semibold text-orange-600 group-hover:underline">Cetak Sekarang &rarr;</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>
