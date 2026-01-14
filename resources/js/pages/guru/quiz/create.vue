<script setup>
import GuruQuizController from '@/actions/App/Http/Controllers/GuruQuizController';
import { Link, useForm } from '@inertiajs/vue3';

// Props dari Controller (Data untuk Dropdown)
defineProps({
    kelas_list: Array,
    matpel_list: Array,
});

const form = useForm({
    title: '',
    kelas_id: '',
    matpel_kode: '',
    duration: 60,
    passing_grade: 75,
    max_attempts: 1,
    start_date: '',
    end_date: '',
    is_randomized: true,
    is_published: false,
});

const submit = () => {
    form.post(GuruQuizController.store().url, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Buat Kuis Baru</h1>
                <p class="mt-2 text-sm text-gray-500">Lengkapi formulir di bawah ini untuk membuat ujian atau latihan baru.</p>
            </div>
            <Link
                href="/guru/quiz"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"
                    />
                </svg>
                Kembali
            </Link>
        </div>

        <form @submit.prevent="submit" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-black/5">
            <div class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 gap-x-8 gap-y-8 p-8 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base leading-7 font-semibold text-gray-900">Informasi Dasar</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">Tentukan judul kuis dan siapa target peserta (Kelas & Mata Pelajaran).</p>
                    </div>

                    <div class="bg-white md:col-span-2">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                            <div class="col-span-full">
                                <label for="title" class="block text-sm leading-6 font-medium text-gray-900">Judul Kuis</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.title"
                                        id="title"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                        placeholder="Contoh: Ulangan Harian Matematika Bab 1"
                                    />
                                    <p v-if="form.errors.title" class="mt-2 text-xs font-medium text-red-600">{{ form.errors.title }}</p>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="kelas" class="block text-sm leading-6 font-medium text-gray-900">Kelas</label>
                                <div class="mt-2">
                                    <select
                                        id="kelas"
                                        v-model="form.kelas_id"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    >
                                        <option value="" disabled>Pilih Kelas</option>
                                        <option v-for="kelas in kelas_list" :key="kelas.id" :value="kelas.id">{{ kelas.nama }}</option>
                                    </select>
                                    <p v-if="form.errors.kelas_id" class="mt-2 text-xs font-medium text-red-600">{{ form.errors.kelas_id }}</p>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="matpel" class="block text-sm leading-6 font-medium text-gray-900">Mata Pelajaran</label>
                                <div class="mt-2">
                                    <select
                                        id="matpel"
                                        v-model="form.matpel_kode"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    >
                                        <option value="" disabled>Pilih Mapel</option>
                                        <option v-for="mp in matpel_list" :key="mp.kode" :value="mp.kode">{{ mp.nama }}</option>
                                    </select>
                                    <p v-if="form.errors.matpel_kode" class="mt-2 text-xs font-medium text-red-600">{{ form.errors.matpel_kode }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-8 gap-y-8 bg-gray-50/30 p-8 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base leading-7 font-semibold text-gray-900">Konfigurasi Teknis</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">Atur durasi, nilai minimal, dan aturan pengacakan soal.</p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-3">
                            <div>
                                <label class="block text-sm leading-6 font-medium text-gray-900">Durasi (Menit)</label>
                                <div class="mt-2">
                                    <input
                                        type="number"
                                        v-model="form.duration"
                                        min="1"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm leading-6 font-medium text-gray-900">KKM (0-100)</label>
                                <div class="mt-2">
                                    <input
                                        type="number"
                                        v-model="form.passing_grade"
                                        min="0"
                                        max="100"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm leading-6 font-medium text-gray-900">Maks. Percobaan</label>
                                <div class="mt-2">
                                    <input
                                        type="number"
                                        v-model="form.max_attempts"
                                        min="1"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    />
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label
                                    class="relative flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 transition-colors hover:bg-gray-50"
                                >
                                    <div class="flex h-6 items-center">
                                        <input
                                            id="randomized"
                                            v-model="form.is_randomized"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                        />
                                    </div>
                                    <div class="text-sm leading-6">
                                        <span class="font-medium text-gray-900">Acak Urutan Soal</span>
                                        <p class="text-gray-500">Jika aktif, setiap siswa akan mendapatkan urutan nomor soal yang berbeda.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-8 gap-y-8 p-8 md:grid-cols-3">
                    <div class="px-4 sm:px-0">
                        <h2 class="text-base leading-7 font-semibold text-gray-900">Jadwal & Status</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">Kapan kuis ini bisa diakses oleh siswa?</p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm leading-6 font-medium text-gray-900">Waktu Mulai</label>
                                <div class="mt-2">
                                    <input
                                        type="datetime-local"
                                        v-model="form.start_date"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    />
                                    <p v-if="form.errors.start_date" class="mt-2 text-xs font-medium text-red-600">{{ form.errors.start_date }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm leading-6 font-medium text-gray-900">Waktu Selesai</label>
                                <div class="mt-2">
                                    <input
                                        type="datetime-local"
                                        v-model="form.end_date"
                                        class="block w-full rounded-md border-0 px-3 py-2.5 text-gray-900 shadow-sm ring-1 ring-gray-300 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:ring-inset sm:text-sm sm:leading-6"
                                    />
                                    <p v-if="form.errors.end_date" class="mt-2 text-xs font-medium text-red-600">{{ form.errors.end_date }}</p>
                                </div>
                            </div>

                            <div class="col-span-full mt-2">
                                <label
                                    class="relative flex cursor-pointer items-start gap-3 rounded-lg border border-yellow-200 bg-yellow-50/50 p-4 transition-colors hover:bg-yellow-50"
                                >
                                    <div class="flex h-6 items-center">
                                        <input
                                            id="publish"
                                            v-model="form.is_published"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-600"
                                        />
                                    </div>
                                    <div class="text-sm leading-6">
                                        <span class="font-bold text-gray-900">Publikasikan Kuis?</span>
                                        <p class="text-gray-600">
                                            Siswa akan dapat melihat kuis ini di dashboard mereka sesuai jadwal yang ditentukan.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-4 border-t border-gray-900/10 bg-gray-50 px-8 py-4">
                <Link href="/guru/quiz" class="text-sm leading-6 font-semibold text-gray-900 hover:text-gray-700">Batal</Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span v-if="form.processing">Menyimpan...</span>
                    <span v-else>Simpan & Lanjut ke Soal</span>
                </button>
            </div>
        </form>
    </div>
</template>
