<script setup>
import KenaikanKelasController from '@/actions/App/Http/Controllers/Admin/KenaikanKelasController';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
// Props dari Controller
const props = defineProps({
    list_kelas: Array,
    siswas: Array,
    selected_kelas_id: String,
    flash: Object,
});

const page = usePage();

// State untuk filter kelas asal
const filterKelas = ref(props.selected_kelas_id || '');

// Fungsi ganti kelas asal
const onFilterChange = () => {
    // MENGGUNAKAN CONTROLLER IMPORT
    router.get(KenaikanKelasController.index().url, { kelas_id: filterKelas.value });
};

// Form Inertia
const form = useForm({
    kelas_tujuan_id: '',
    tingkat_baru: '',
    nis_list: [],
});

// -- Logic Checkbox "Pilih Semua" --
const checkAll = computed({
    get() {
        return props.siswas && props.siswas.length > 0 ? form.nis_list.length === props.siswas.length : false;
    },
    set(val) {
        if (val) {
            form.nis_list = props.siswas.map((s) => s.nis);
        } else {
            form.nis_list = [];
        }
    },
});

watch(
    () => form.kelas_tujuan_id,
    (newVal) => {
        const targetKelas = props.list_kelas.find((k) => k.id == newVal);
        if (targetKelas) {
            form.tingkat_baru = targetKelas.tingkat;
        }
    },
);

const submit = () => {
    if (confirm('Yakin ingin menaikkan kelas siswa yang dipilih?')) {
        form.post(KenaikanKelasController.store().url, {
            onSuccess: () => {
                form.reset('nis_list');
                toast.success('Kenaikan kelas berhasil!');
            },
        });
    }
};
const groupedKelas = computed(() => {
    const groups = {};

    const sortedList = [...props.list_kelas].sort((a, b) => a.tingkat - b.tingkat);
    sortedList.forEach((kelas) => {
        const key = kelas.tingkat;
        if (!groups[key]) {
            groups[key] = [];
        }
        groups[key].push(kelas);
    });

    return groups;
});
</script>

<template>
    <div class="p-6">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Proses Kenaikan Kelas</h1>

            <div v-if="$page.props.flash?.message" class="rounded bg-green-100 px-4 py-2 text-sm text-green-800">
                {{ $page.props.flash.message }}
            </div>
        </div>

        <div class="mb-6 rounded border-l-4 border-blue-500 bg-white p-4 shadow">
            <label class="mb-2 block font-semibold">Pilih Kelas Asal:</label>
            <div class="flex gap-4">
                <select v-model="filterKelas" @change="onFilterChange" class="w-full rounded border p-2 md:w-1/3">
                    <option value="">-- Pilih Kelas --</option>

                    <optgroup v-for="(kelas_list, tingkat) in groupedKelas" :key="tingkat" :label="'Tingkat ' + tingkat">
                        <option v-for="kelas in kelas_list" :key="kelas.id" :value="kelas.id">
                            {{ kelas.nama }}
                        </option>
                    </optgroup>
                </select>
            </div>
        </div>

        <div v-if="siswas.length > 0" class="flex flex-col gap-6 md:flex-row">
            <div class="flex-1 rounded bg-white p-4 shadow">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="font-bold">Daftar Siswa</h3>
                    <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">Terpilih: {{ form.nis_list.length }}</span>
                </div>

                <table class="w-full border-collapse text-left">
                    <thead class="bg-gray-100">
                        <tr class="border-b border-b-gray-200">
                            <th class="w-10 p-2 text-center">
                                <input type="checkbox" v-model="checkAll" />
                            </th>
                            <th class="p-2">NIS</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="siswa in siswas" :key="siswa.nis" class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="p-2 text-center">
                                <input type="checkbox" :value="siswa.nis" v-model="form.nis_list" />
                            </td>
                            <td class="p-2 font-mono text-sm">{{ siswa.nis }}</td>
                            <td class="p-2">{{ siswa.user?.name ?? 'Nama tidak ditemukan' }}</td>
                            <td class="p-2 text-sm text-gray-500">{{ siswa.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="sticky top-4 h-fit w-full rounded border-t-4 border-orange-500 bg-gray-50 p-4 shadow md:w-1/3">
                <h3 class="mb-4 text-lg font-bold">Aksi Kenaikan</h3>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-semibold">Pindah ke Kelas:</label>
                    <select v-model="form.kelas_tujuan_id" class="w-full rounded border bg-white p-2">
                        <option value="">-- Pilih Kelas Tujuan --</option>

                        <optgroup v-for="(kelas_list, tingkat) in groupedKelas" :key="tingkat" :label="'Tingkat ' + tingkat">
                            <option v-for="kelas in kelas_list" :key="kelas.id" :value="kelas.id">
                                {{ kelas.nama }}
                            </option>
                        </optgroup>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="mb-1 block text-sm font-semibold">Ubah ke Tingkat:</label>
                    <select v-model="form.tingkat_baru" class="w-full rounded border bg-white p-2">
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="10">10 (X)</option>
                        <option value="11">11 (XI)</option>
                        <option value="12">12 (XII)</option>
                        <option value="13">Lulus / Alumni</option>
                    </select>
                    <div v-if="form.errors.tingkat_baru" class="mt-1 text-sm text-red-500">{{ form.errors.tingkat_baru }}</div>
                </div>

                <div class="mb-4 rounded bg-blue-100 p-3 text-sm text-blue-800">
                    <span class="font-bold">{{ form.nis_list.length }}</span> siswa terpilih akan dipindahkan.
                </div>

                <button
                    @click="submit"
                    :disabled="form.processing || form.nis_list.length === 0"
                    class="w-full rounded bg-blue-600 py-2 font-bold text-white shadow hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ form.processing ? 'Memproses...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </div>

        <div v-else-if="selected_kelas_id" class="mt-6 rounded bg-white p-10 text-center text-gray-500 shadow">
            Tidak ada siswa aktif di kelas ini.
        </div>

        <div v-else class="mt-6 rounded bg-white p-10 text-center text-gray-500 shadow">Silakan pilih kelas asal terlebih dahulu.</div>
    </div>
</template>
