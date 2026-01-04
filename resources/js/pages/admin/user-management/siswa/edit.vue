<script setup lang="ts">
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Camera, RefreshCw, Save } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    kelasList: { id: number; nama: string; tingkat: string }[];
    siswa: any;
}>();

const breadcrumbs = [{ label: 'Dashboard' }, { label: 'User Management' }, { label: 'Siswa' }, { label: 'Edit' }];

const form = useForm({
    name: props.siswa.user?.name || props.siswa.name || '',
    nis: props.siswa.nis,
    jenis_kelamin: props.siswa.jenis_kelamin,
    agama: props.siswa.agama,
    tahun_masuk: props.siswa.tahun_masuk,
    tingkat: props.siswa.tingkat,
    kelas_id: props.siswa.kelas_id,
    status: props.siswa.status,
    pas_photo: null as File | null,
});

const filteredKelas = computed(() => {
    return props.kelasList.filter((k: any) => k.tingkat == form.tingkat);
});

watch(
    () => form.tingkat,
    (newVal, oldVal) => {
        if (oldVal !== undefined && newVal != props.siswa.tingkat) {
            form.kelas_id = '';
        }
    },
);

// PERBAIKAN: Default null. Jangan isi dengan data DB di sini.
// Biarkan template yang memisahkan antara preview vs data DB.
const photoPreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];

        // Validasi ukuran client-side
        if (file.size > 2048 * 1024) {
            toast.error('Ukuran file terlalu besar (Max 2MB)');
            return;
        }

        form.pas_photo = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const submit = () => {
    // Gunakan POST, tapi payloadnya mengandung _method: PUT
    form.post(
        UserManagementController.updateSiswa({
            id: props.siswa.nis,
        }).url,
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Data siswa berhasil diperbarui!', {
                    position: 'top-center',
                });
                form.pas_photo = null;
                photoPreview.value = null; // Reset preview agar kembali menampilkan data DB (yang baru)
            },
            onError: (errors: any) => {
                toast.error('Gagal memperbarui data. Periksa inputan Anda.', {
                    position: 'top-center',
                });
                console.error(errors);
            },
        },
    );
};
</script>

<template>
    <div class="mb-6 flex flex-col gap-4 px-1 sm:flex-row sm:items-center sm:justify-between">
        <Breadcrumb :items="breadcrumbs" />

        <Link
            :href="UserManagementController.siswa().url"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm hover:bg-neutral-50"
        >
            <ArrowLeft class="h-4 w-4" />
            Kembali
        </Link>
    </div>

    <div class="px-4 pb-10">
        <form @submit.prevent="submit" class="flex flex-col gap-6 lg:flex-row">
            <div class="w-full lg:w-1/4">
                <div class="sticky top-6 flex flex-col items-center rounded-xl border border-neutral-200 bg-white p-6 text-center shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold text-neutral-700">Foto Profil</h3>

                    <div
                        class="group relative h-32 w-32 cursor-pointer overflow-hidden rounded-full border-2 transition-colors"
                        :class="form.errors.pas_photo ? 'border-red-500' : 'border-dashed border-neutral-300 hover:border-indigo-500'"
                        @click="triggerFileInput"
                    >
                        <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />

                        <img v-else-if="props.siswa.pas_photo" :src="`/storage/${props.siswa.pas_photo}`" class="h-full w-full object-cover" />

                        <div
                            v-else
                            class="flex h-full w-full flex-col items-center justify-center bg-neutral-50 text-neutral-400 group-hover:text-indigo-500"
                        >
                            <Camera class="mb-1 h-8 w-8" />
                            <span class="text-[10px]">Upload</span>
                        </div>

                        <div
                            class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100"
                        >
                            <span class="text-xs font-medium text-white">Ganti</span>
                        </div>
                    </div>

                    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handlePhotoChange" />

                    <p class="mt-4 text-[10px] text-neutral-400">Format: JPG, PNG. Maks 2MB.</p>
                    <div v-if="form.errors.pas_photo" class="mt-2 text-xs text-red-500">
                        {{ form.errors.pas_photo }}
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-100 px-6 py-4">
                        <h2 class="text-base font-semibold text-neutral-800">Edit Informasi Siswa</h2>
                        <p class="text-xs text-neutral-500">Perbarui data diri siswa & akun pengguna.</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="col-span-1 md:col-span-2">
                                <label class="mb-1.5 block text-xs font-medium text-neutral-700">Nama Lengkap</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    placeholder="Contoh: Dimas Aditya"
                                />
                                <span v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</span>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-neutral-700">NIS<sup class="text-red-500">*</sup></label>
                                <input
                                    v-model="form.nis"
                                    type="number"
                                    class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Nomor Induk Siswa"
                                />
                                <span v-if="form.errors.nis" class="text-xs text-red-500">{{ form.errors.nis }}</span>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-neutral-700">Jenis Kelamin</label>
                                <select
                                    v-model="form.jenis_kelamin"
                                    class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                >
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-neutral-700">Agama</label>
                                <select
                                    v-model="form.agama"
                                    class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                >
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>

                            <div class="col-span-1 grid grid-cols-1 gap-4 md:col-span-2 md:grid-cols-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-neutral-700">Tahun Masuk</label>
                                    <input
                                        v-model="form.tahun_masuk"
                                        type="number"
                                        class="w-full rounded-lg border border-neutral-200 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-neutral-700">Tingkat</label>
                                    <select
                                        v-model="form.tingkat"
                                        class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    >
                                        <option value="10">X (Sepuluh)</option>
                                        <option value="11">XI (Sebelas)</option>
                                        <option value="12">XII (Dua Belas)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-xs font-medium text-neutral-700">Kelas</label>
                                    <select
                                        v-model="form.kelas_id"
                                        :disabled="filteredKelas.length === 0"
                                        class="w-full rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 disabled:bg-neutral-100 disabled:text-neutral-400"
                                    >
                                        <option value="" disabled>Pilih Kelas</option>
                                        <option v-for="k in filteredKelas" :key="k.id" :value="k.id">
                                            {{ k.nama }}
                                        </option>
                                    </select>
                                    <div v-if="filteredKelas.length === 0" class="mt-1 text-[10px] text-amber-600">
                                        *Tidak ada kelas untuk tingkat {{ form.tingkat }}
                                    </div>
                                    <span v-if="form.errors.kelas_id" class="text-xs text-red-500">{{ form.errors.kelas_id }}</span>
                                </div>
                            </div>

                          <div class="col-span-1 md:col-span-2">
    <label class="mb-1.5 block text-xs font-medium text-neutral-700">Status Siswa</label>
    <div class="flex flex-wrap items-center gap-6 pt-1">
        
        <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-600 transition hover:text-indigo-600">
            <input type="radio" v-model="form.status" value="aktif" class="text-indigo-600 focus:ring-indigo-500" />
            <span class="font-medium">Aktif</span>
        </label>

        <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-600 transition hover:text-indigo-600">
            <input type="radio" v-model="form.status" value="lulus" class="text-indigo-600 focus:ring-indigo-500" />
            <span class="font-medium">Lulus</span>
        </label>

        <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-600 transition hover:text-indigo-600">
            <input type="radio" v-model="form.status" value="keluar" class="text-indigo-600 focus:ring-indigo-500" />
            <span class="font-medium">Keluar</span>
        </label>

        <label class="flex cursor-pointer items-center gap-2 text-sm text-neutral-600 transition hover:text-indigo-600">
            <input type="radio" v-model="form.status" value="tinggal_kelas" class="text-indigo-600 focus:ring-indigo-500" />
            <span class="font-medium">Tinggal Kelas</span>
        </label>

    </div>
</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 rounded-b-xl border-t border-neutral-100 bg-neutral-50 px-6 py-4">
                        <Link
                            :href="UserManagementController.siswa().url"
                            class="rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 active:scale-95"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 active:scale-95 disabled:opacity-50"
                        >
                            <RefreshCw v-if="form.processing" class="h-4 w-4 animate-spin" />
                            <Save v-else class="h-4 w-4" />
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
