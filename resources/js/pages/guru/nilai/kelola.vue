<template>

    <div class="min-h-screen bg-gray-50/50 p-6 font-sans text-gray-800">
        <div class="mx-auto mb-8 max-w-7xl">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-center">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Daftar Nilai Siswa</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola data akademik dan evaluasi hasil belajar siswa.</p>
                </div>

                <div class="flex w-full items-center rounded-xl border border-gray-200 bg-white p-1 shadow-sm md:w-72">
                    <div class="pl-3 text-gray-400">
                        <FilterIcon class="h-5 w-5" />
                    </div>
                    <select
                        v-model="filterSiswaId"
                        @change="fetchData"
                        class="w-full cursor-pointer border-none bg-transparent py-2.5 text-sm font-medium text-gray-700 focus:ring-0"
                    >
                        <option value="">Tampilkan Semua Siswa</option>
                        <option v-for="siswa in listSiswaOptions" :key="siswa.id" :value="siswa.id">
                            {{ siswa.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl">
            <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div v-if="isLoading" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm">
                    <svg class="mb-3 h-10 w-10 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    <span class="text-sm font-medium text-indigo-600">Memuat data...</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase">Siswa</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                    Mata Pelajaran / Tugas
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold tracking-wider text-gray-500 uppercase">Nilai</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                    Komentar Guru
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold tracking-wider text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="nilai in nilaiList" :key="nilai.id" class="group transition-colors duration-200 hover:bg-gray-50/80">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="mr-3 flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-600"
                                        >
                                            {{ getInitials(nilai.siswa?.name) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ nilai.siswa?.name || 'Unknown' }}</div>
                                            <div class="text-xs text-gray-400">ID: {{ String(nilai.siswa.id).substring(0, 6) }}...</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-800">{{ nilai.tugas?.title || '-' }}</span>
                                        <span class="mt-1 w-fit rounded-full bg-indigo-50 px-2 py-0.5 text-xs text-indigo-500">
                                            {{ nilai.tugas?.matpel?.nama || 'Umum' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full border px-3 py-1 text-sm leading-5 font-bold"
                                        :class="getGradeColor(nilai.angka)"
                                    >
                                        {{ nilai.angka }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="max-w-xs truncate text-sm text-gray-500" :title="nilai.komentar">
                                        {{ nilai.komentar || '-' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-100 transition-opacity sm:opacity-0 sm:group-hover:opacity-100"
                                    >
                                        <button
                                            @click="openEditModal(nilai)"
                                            class="rounded-lg bg-indigo-50 p-2 text-indigo-600 transition-colors hover:bg-indigo-100"
                                            title="Edit Nilai"
                                        >
                                            <PencilIcon class="h-4 w-4" />
                                        </button>
                                        <button
                                            @click="deleteNilai(nilai.id)"
                                            class="rounded-lg bg-red-50 p-2 text-red-600 transition-colors hover:bg-red-100"
                                            title="Hapus Nilai"
                                        >
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!isLoading && nilaiList.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <InboxIcon class="mb-3 h-12 w-12 text-gray-300" />
                                        <p class="text-base font-medium text-gray-500">Belum ada data nilai.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <template>
            <Teleport to="body">
                <Transition name="modal">
                    <div
                        v-if="showModal"
                        class="fixed inset-0 z-[9999] overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="bg-opacity-40 fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

                            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                            <div
                                class="relative z-10 inline-block w-full transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:max-w-lg sm:align-middle"
                            >
                                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3 sm:px-6">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Update Penilaian</h3>
                                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600"><XIcon class="h-5 w-5" /></button>
                                </div>
                                <form @submit.prevent="updateNilai" class="p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Nilai Akhir</label>
                                            <input
                                                v-model.number="form.angka"
                                                type="number"
                                                min="0"
                                                max="100"
                                                class="block w-full border rounded-lg border-gray-300 py-2.5 pl-4 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Komentar</label>
                                            <textarea
                                                v-model="form.komentar"
                                                rows="4"
                                                class="block w-full border rounded-lg border-gray-300 p-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-8 flex justify-end gap-3">
                                        <button
                                            type="button"
                                            @click="closeModal"
                                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                        >
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </Transition>
            </Teleport>
        </template>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
// Import Toaster dan toast dari vue-sonner
import NilaiController from '@/actions/App/Http/Controllers/Guru/NilaiController';
import { FilterIcon, InboxIcon, PencilIcon, TrashIcon, XIcon } from 'lucide-vue-next';
import { Toaster, toast } from 'vue-sonner';

// State
const nilaiList = ref([]);
const listSiswaOptions = ref([]);
const filterSiswaId = ref('');
const isLoading = ref(false);
const showModal = ref(false);

const form = ref({
    id: null,
    angka: 0,
    komentar: '',
});

// Helper Functions
const getInitials = (name) => (name ? name.charAt(0).toUpperCase() : '?');

const getGradeColor = (nilai) => {
    if (nilai >= 85) return 'bg-green-100 text-green-700 border-green-200';
    if (nilai >= 75) return 'bg-blue-100 text-blue-700 border-blue-200';
    if (nilai >= 60) return 'bg-yellow-100 text-yellow-700 border-yellow-200';
    return 'bg-red-100 text-red-700 border-red-200';
};

// --- API Calls ---

const fetchData = async () => {
    isLoading.value = true;
    try {
        const params = {};
        if (filterSiswaId.value) params.siswa_id = filterSiswaId.value;

        const response = await axios.get(NilaiController.index().url, { params });
        nilaiList.value = response.data.nilai;
        if (listSiswaOptions.value.length === 0) {
            listSiswaOptions.value = response.data.siswa;
        }
    } catch (error) {
        console.error('Gagal mengambil data:', error);
        toast.error('Gagal memuat data dari server');
    } finally {
        isLoading.value = false;
    }
};

const deleteNilai = async (id) => {
    if (!confirm('Apakah Anda yakin ingin menghapus nilai ini?')) return;

    // Tampilkan loading toast (opsional, tapi bagus untuk UX)
    const loadingToast = toast.loading('Menghapus data...');

    try {
        await axios.delete(NilaiController.destroy({ id: id }).url);
        fetchData();

        // Ganti loading toast dengan success
        toast.dismiss(loadingToast);
        toast.success('Data nilai berhasil dihapus!');
    } catch (error) {
        toast.dismiss(loadingToast);
        toast.error('Gagal menghapus data nilai.');
    }
};

const updateNilai = async () => {
    const loadingToast = toast.loading('Menyimpan perubahan...');

    try {
        await axios.put(NilaiController.update({ id: form.value.id }).url, {
            angka: form.value.angka,
            komentar: form.value.komentar,
        });
        closeModal();
        fetchData();

        toast.dismiss(loadingToast);
        toast.success('Data nilai berhasil diperbarui!');
    } catch (error) {
        console.error(error);
        toast.dismiss(loadingToast);
        toast.error('Gagal memperbarui data.');
    }
};

// --- Modal Logic ---
const openEditModal = (item) => {
    form.value = {
        id: item.id,
        angka: item.angka,
        komentar: item.komentar,
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

// --- Lifecycle ---
onMounted(() => {
    fetchData();
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
