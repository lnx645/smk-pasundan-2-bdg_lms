<script setup lang="ts">
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import { useForm } from '@inertiajs/vue3';
import { Download, FileSpreadsheet, Upload, X } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const emit = defineEmits(['close']);

const form = useForm({
    file: null as File | null,
});

const handleFile = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file = target.files[0];
    }
};

const submit = () => {
    if (!form.file) return toast.error('Pilih file excel terlebih dahulu');

    form.post(UserManagementController.importSiswa().url, {
        onSuccess: () => {
            toast.success('Import Data Siswa Berhasil!');
            emit('close'); // Tutup modal jika sukses
            form.reset();
        },
        onError: (err) => {
            // Menampilkan error validasi atau error exception dari controller
            toast.error(err.message || 'Gagal import data. Periksa format file.');
        },
    });
};
</script>

<template>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Import Siswa dari Excel</h3>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 transition-colors">
                <X class="h-5 w-5" />
            </button>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-green-50 p-4 border border-green-100">
                <div class="flex items-start gap-3">
                    <FileSpreadsheet class="h-5 w-5 text-green-600 mt-0.5" />
                    <div>
                        <h4 class="text-sm font-semibold text-green-900">1. Download Template</h4>
                        <p class="text-xs text-green-700 mt-1">
                            Gunakan template ini. Pastikan <b>Nama Kelas</b> sesuai dengan data di sistem.
                        </p>
                        <a 
                            :href="UserManagementController.downloadTemplateSiswa().url" 
                            class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-green-700 hover:text-green-800 hover:underline"
                        >
                            <Download class="h-3.5 w-3.5" />
                            Download Template .XLSX
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">2. Upload File Excel</label>
                <div 
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-green-400 transition-colors"
                    :class="{'border-green-500 bg-green-50': form.file}"
                >
                    <div class="space-y-1 text-center">
                        <Upload class="mx-auto h-12 w-12 text-gray-400" />
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label class="relative cursor-pointer rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                <span>{{ form.file ? form.file.name : 'Klik untuk Pilih File Excel' }}</span>
                                <input type="file" class="sr-only" accept=".xlsx, .xls, .csv" @change="handleFile">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Format: .xlsx, .xls, .csv (Maks 2MB)</p>
                    </div>
                </div>
                
                <div v-if="form.errors?.file" class="mt-2 p-2 rounded bg-red-50 text-xs text-red-600 border border-red-100">
                    <span class="font-bold">Error:</span> {{ form.errors?.file }}
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Batal
                </button>
                <button
                    @click="submit"
                    :disabled="form.processing || !form.file"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                >
                    {{ form.processing ? 'Sedang Mengimport...' : 'Mulai Import' }}
                </button>
            </div>
        </div>
    </div>
</template>