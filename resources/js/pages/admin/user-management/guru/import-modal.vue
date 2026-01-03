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
    if (target.files) {
        form.file = target.files[0];
    }
};

const submit = () => {
    if (!form.file) return toast.error('Pilih file excel terlebih dahulu');

    // Pastikan route ini ada di web.php: name('user-management.importGuru')
    form.post(UserManagementController.importGuru().url, {
        onSuccess: () => {
            toast.success('Import Berhasil!');
            emit('close');
        },
        onError: (err) => {
            toast.error(err.message || 'Gagal import data');
        },
    });
};
</script>

<template>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Import Guru dari Excel</h3>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                <X class="h-5 w-5" />
            </button>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-blue-50 p-4 border border-blue-100">
                <div class="flex items-start gap-3">
                    <FileSpreadsheet class="h-5 w-5 text-blue-600 mt-0.5" />
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900">1. Download Template</h4>
                        <p class="text-xs text-blue-700 mt-1">Gunakan template ini agar format data sesuai sistem.</p>
                        <a 
                            :href="UserManagementController.downloadTemplateGuru().url" 
                            class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-blue-700 hover:text-blue-800 hover:underline"
                        >
                            <Download class="h-3.5 w-3.5" />
                            Download Template .CSV
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">2. Upload File Excel/CSV</label>
                <div 
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors"
                    :class="{'border-indigo-500 bg-indigo-50': form.file}"
                >
                    <div class="space-y-1 text-center">
                        <Upload class="mx-auto h-12 w-12 text-gray-400" />
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                <span>{{ form.file ? form.file.name : 'Pilih File Excel' }}</span>
                                <input type="file" class="sr-only" accept=".xlsx, .xls, .csv" @change="handleFile">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">XLSX, XLS, atau CSV (Max 2MB)</p>
                    </div>
                </div>
                <div v-if="(form.errors as any)?.message" class="mt-2 text-xs text-red-600">
                    {{ (form.errors as any)?.message }}
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button
                    @click="submit"
                    :disabled="form.processing || !form.file"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ form.processing ? 'Sedang Mengimport...' : 'Mulai Import' }}
                </button>
            </div>
        </div>
    </div>
</template>