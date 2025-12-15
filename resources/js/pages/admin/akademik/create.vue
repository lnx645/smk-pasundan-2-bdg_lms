<script setup>
import { store } from '@/routes/admin/akademik/akademik/plotting';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    matpels: Array,
    gurus: Array,
    kelas_group: Object, // Object grouping: { '10': [], '11': [] }
});

const form = useForm({
    matpel_kode: '',
    guru_nip: '',
    kelas_ids: [],
});

// Fungsi untuk Toggle Select All per Tingkat
const toggleTingkat = (tingkat, isChecked) => {
    const kelasDiTingkatIni = props.kelas_group[tingkat].map((k) => k.id);

    if (isChecked) {
        // Tambahkan semua ID kelas tingkat ini ke array form.kelas_ids (jika belum ada)
        kelasDiTingkatIni.forEach((id) => {
            if (!form.kelas_ids.includes(id)) {
                form.kelas_ids.push(id);
            }
        });
    } else {
        // Hapus ID kelas tingkat ini dari array
        form.kelas_ids = form.kelas_ids.filter((id) => !kelasDiTingkatIni.includes(id));
    }
};

const submit = () => {
    form.post(store().url, {
        onSuccess: () => {
            form.reset('kelas_ids');
            alert('Data berhasil disimpan!');
        },
    });
};
const breadcrumbs = [
    { label: 'Dashboard' },
    {
        label: 'Akademik',
    },
];
</script>

<template>
    <Breadcrumb :items="breadcrumbs" />
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                <form @submit.prevent="submit">
                    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                            <select
                                v-model="form.matpel_kode"
                                class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="" disabled>-- Pilih Mapel --</option>
                                <option v-for="m in matpels" :key="m.kode" :value="m.kode">{{ m.nama }} ({{ m.kode }})</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Guru Pengajar</label>
                            <select
                                v-model="form.guru_nip"
                                class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="" disabled>-- Pilih Guru --</option>
                                <option v-for="g in gurus" :key="g.nip" :value="g.nip">
                                    {{ g.nama }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <hr class="mb-6 border-gray-200" />

                    <h3 class="mb-4 text-lg font-medium text-gray-900">Pilih Kelas Sasaran</h3>
                    <p class="mb-4 text-sm text-gray-500">Centang kelas yang akan menerima mata pelajaran ini.</p>

                    <div v-if="Object.keys(kelas_group).length === 0" class="text-red-500">Belum ada data kelas.</div>

                    <div v-for="(kelasList, tingkat) in kelas_group" :key="tingkat" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="mb-3 flex items-center justify-between border-b border-gray-300 pb-2">
                            <h4 class="font-bold text-gray-700">Tingkat {{ tingkat }}</h4>

                            <label class="flex cursor-pointer items-center space-x-2 text-sm font-semibold text-indigo-600 select-none">
                                <input
                                    type="checkbox"
                                    @change="(e) => toggleTingkat(tingkat, e.target.checked)"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span>Pilih Semua Kelas {{ tingkat }}</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
                            <div v-for="kelas in kelasList" :key="kelas.id" class="flex items-center">
                                <label
                                    class="flex w-full cursor-pointer items-center space-x-2 rounded border border-gray-200 bg-white px-3 py-2 select-none hover:border-indigo-300"
                                >
                                    <input
                                        type="checkbox"
                                        :value="kelas.id"
                                        v-model="form.kelas_ids"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <span class="text-sm text-gray-700">{{ kelas.nama }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-bold text-white shadow-lg transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <svg
                                v-if="!form.processing"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                />
                            </svg>
                            <span v-else>Menyimpan...</span>
                            Simpan Distribusi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
