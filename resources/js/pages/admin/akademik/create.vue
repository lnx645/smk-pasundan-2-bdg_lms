<script setup>
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { store } from '@/routes/admin/akademik/akademik/plotting';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    matpels: Array,
    gurus: Array,
    kelas_group: Object,
});

const form = useForm({
    matpel_kode: '',
    guru_nip: '',
    kelas_ids: [],
});

const showAllGuru = ref(false);

// --- LOGIKA FILTER GURU ---
const filteredGurus = computed(() => {
    if (!form.matpel_kode) return [];
    if (showAllGuru.value) return props.gurus;
    return props.gurus.filter((guru) => guru.keahlian_utama_kode == form.matpel_kode);
});

// Reset saat ganti mapel
watch(
    () => form.matpel_kode,
    () => {
        form.guru_nip = '';
        showAllGuru.value = false;
    },
);

const toggleTingkat = (tingkat, isChecked) => {
    const kelasDiTingkatIni = props.kelas_group[tingkat].map((k) => k.id);
    if (isChecked) {
        kelasDiTingkatIni.forEach((id) => {
            if (!form.kelas_ids.includes(id)) form.kelas_ids.push(id);
        });
    } else {
        form.kelas_ids = form.kelas_ids.filter((id) => !kelasDiTingkatIni.includes(id));
    }
};

const submit = () => {
    form.post(store().url, {
        onSuccess: () => {
            form.reset('kelas_ids', 'matpel_kode', 'guru_nip');
            showAllGuru.value = false;
            alert('Data berhasil disimpan!');
        },
    });
};
const breadcrumbs = [{ label: 'Dashboard' }, { label: 'Akademik' }, { label: 'Plotting Jadwal' }];
</script>

<template>
    <Breadcrumb :items="breadcrumbs" />

    <div class="min-h-screen bg-gray-50 py-4">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="mb-6 px-2 sm:px-0">
                <h2 class="text-2xl font-bold text-gray-800">Distribusi Mata Pelajaran</h2>
                <p class="text-sm text-gray-500">Atur pengajar dan kelas target untuk setiap mata pelajaran.</p>
            </div>
            <div class="overflow-hidden border border-gray-100 bg-white sm:rounded-2xl">
                <form @submit.prevent="submit">
                    <div class="border-b border-gray-100 bg-indigo-50/50 p-8">
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div class="group relative">
                                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-indigo-500"
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
                                    Mata Pelajaran
                                </label>
                                <div class="relative">
                                    <select
                                        v-model="form.matpel_kode"
                                        class="w-full rounded-xl border-gray-300 bg-white py-3 pr-10 pl-4 text-sm shadow-sm transition-all group-hover:border-indigo-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                        required
                                    >
                                        <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                                        <option v-for="m in matpels" :key="m.kode" :value="m.kode">{{ m.nama }} ({{ m.kode }})</option>
                                    </select>
                                </div>
                            </div>

                            <div class="relative">
                                <div class="mb-2 flex items-center justify-between">
                                    <label class="block flex items-center gap-2 text-sm font-bold text-gray-700">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-indigo-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        Guru Pengajar
                                    </label>

                                    <div v-if="form.matpel_kode" class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-gray-500">Semua Guru</span>
                                        <button
                                            type="button"
                                            @click="showAllGuru = !showAllGuru"
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                            :class="showAllGuru ? 'bg-indigo-600' : 'bg-gray-200'"
                                        >
                                            <span
                                                class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform"
                                                :class="showAllGuru ? 'translate-x-5' : 'translate-x-1'"
                                            />
                                        </button>
                                    </div>
                                </div>

                                <div class="group relative">
                                    <select
                                        v-model="form.guru_nip"
                                        class="w-full rounded-xl border-gray-300 bg-white py-3 pr-10 pl-4 text-sm shadow-sm transition-all group-hover:border-indigo-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 disabled:bg-gray-100 disabled:text-gray-400"
                                        required
                                        :disabled="!form.matpel_kode"
                                    >
                                        <option value="" disabled>
                                            {{ !form.matpel_kode ? 'Pilih Mapel Terlebih Dahulu...' : '-- Pilih Guru Pengajar --' }}
                                        </option>

                                        <option v-for="g in filteredGurus" :key="g.nip" :value="g.nip">
                                            {{ g.nama }}
                                            <template v-if="g.keahlian_utama"> ({{ g.keahlian_utama }}) </template>
                                            <template v-if="showAllGuru && g.keahlian_utama_kode != form.matpel_kode && form.matpel_kode">
                                                [Lintas Minat]
                                            </template>
                                        </option>

                                        <option v-if="form.matpel_kode && filteredGurus.length === 0" disabled>
                                            (Tidak ada guru dengan spesialisasi ini)
                                        </option>
                                    </select>
                                </div>

                                <div
                                    v-if="form.matpel_kode && filteredGurus.length === 0 && !showAllGuru"
                                    class="mt-2 flex animate-pulse items-center gap-2 rounded-md border border-red-100 bg-red-50 p-2 text-xs text-red-600"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span>Tidak ada guru spesialis. Aktifkan toggle di atas untuk melihat semua guru.</span>
                                </div>
                                <div
                                    v-if="showAllGuru"
                                    class="mt-2 flex items-center gap-2 rounded-md border border-orange-100 bg-orange-50 p-2 text-xs text-orange-600"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <span>Mode Lintas Minat: Menampilkan seluruh guru.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="mb-6 flex items-center gap-2 text-lg font-bold text-gray-800">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    />
                                </svg>
                            </div>
                            Pilih Kelas Sasaran
                        </h3>

                        <div
                            v-if="Object.keys(kelas_group).length === 0"
                            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 py-10 text-gray-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mb-2 h-10 w-10 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                            Belum ada data kelas tersedia.
                        </div>

                        <div v-for="(kelasList, tingkat) in kelas_group" :key="tingkat" class="mb-8">
                            <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-2">
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-bold text-gray-700">Tingkat {{ tingkat }}</span>

                                <label
                                    class="flex cursor-pointer items-center space-x-2 text-sm font-medium text-indigo-600 transition select-none hover:text-indigo-800"
                                >
                                    <input
                                        type="checkbox"
                                        @change="(e) => toggleTingkat(tingkat, e.target.checked)"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 opacity-0 focus:ring-indigo-500"
                                    />
                                    <span>Pilih Semua Tingkat {{ tingkat }}</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                                <div v-for="kelas in kelasList" :key="kelas.id">
                                    <label
                                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border p-3 text-center shadow-sm transition-all duration-200 select-none hover:shadow-md"
                                        :class="
                                            form.kelas_ids.includes(kelas.id)
                                                ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500'
                                                : 'border-gray-200 bg-white hover:border-indigo-300'
                                        "
                                    >
                                        <input
                                            type="checkbox"
                                            :value="kelas.id"
                                            v-model="form.kelas_ids"
                                            class="absolute top-2 right-2 hidden h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="mb-2 h-6 w-6"
                                            :class="form.kelas_ids.includes(kelas.id) ? 'text-indigo-600' : 'text-gray-400'"
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

                                        <span
                                            class="text-sm font-semibold"
                                            :class="form.kelas_ids.includes(kelas.id) ? 'text-indigo-900' : 'text-gray-700'"
                                        >
                                            {{ kelas.nama }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-8 py-5">
                        <div class="text-sm text-gray-500">
                            <span class="font-bold text-gray-800">{{ form.kelas_ids.length }}</span> kelas dipilih.
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 font-bold text-white shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl disabled:translate-y-0 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none"
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
                            <svg v-else class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Distribusi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
