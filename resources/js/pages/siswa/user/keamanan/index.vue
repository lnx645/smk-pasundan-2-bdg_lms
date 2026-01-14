<script setup>
import SiswaSecurityController from '@/actions/App/Http/Controllers/SiswaSecurityController';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props
const props = defineProps({
    auth: Object,
});

// State untuk toggle password visibility
const showPassword = ref(false);

// Daftar Pilihan Pertanyaan
const questionOptions = [
    'Siapa nama ibu kandung?',
    'Apa makanan kesukaan saat kecil?',
    'Apa nama hewan peliharaan pertama?',
    'Di kota mana ayah lahir?',
    'Apa judul lagu favoritmu?',
];

const form = useForm({
    question: props.auth.user.security_question || '',
    answer: '',
    current_password: '',
});

const submit = () => {
    form.post(SiswaSecurityController.update().url, {
        onSuccess: () => {
            form.reset('answer', 'current_password');
        },
    });
};
</script>

<template>
    <div class="mt-5 max-w-3xl">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0D1B6E]/10 text-[#0D1B6E]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"
                    />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Keamanan Akun</h2>
                <p class="text-sm text-gray-500">Atur pertanyaan rahasia untuk memulihkan akun jika lupa password.</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-lg shadow-gray-100/50">
            <div class="bg-[#0D1B6E] px-6 py-4">
                <h3 class="font-semibold text-white">Formulir Keamanan</h3>
            </div>

            <div class="p-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="mb-2 block pl-1 text-sm font-semibold text-gray-700">Pilih Pertanyaan Keamanan</label>
                        <div class="relative">
                            <select
                                v-model="form.question"
                                class="w-full appearance-none rounded-2xl border-none bg-[#F5F7FA] px-5 py-3 text-gray-700 transition-all focus:ring-2 focus:ring-[#0D1B6E] focus:outline-none"
                            >
                                <option value="" disabled>-- Silakan Pilih Pertanyaan --</option>
                                <option v-for="q in questionOptions" :key="q" :value="q">{{ q }}</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </div>
                        </div>
                        <div v-if="form.errors.question" class="mt-1 pl-2 text-xs font-medium text-red-500">{{ form.errors.question }}</div>
                    </div>

                    <div>
                        <label class="mb-2 block pl-1 text-sm font-semibold text-gray-700">Jawaban Anda</label>
                        <input
                            v-model="form.answer"
                            type="text"
                            placeholder="Tulis jawaban rahasia..."
                            class="w-full rounded-2xl border-none bg-[#F5F7FA] px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:ring-2 focus:ring-[#0D1B6E] focus:outline-none"
                        />
                        <p class="mt-2 pl-1 text-xs text-gray-400">
                            <span class="mr-1 inline-block rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-bold text-gray-600">INFO</span>
                            Jawaban tidak sensitif huruf besar/kecil (Case Insensitive).
                        </p>
                        <div v-if="form.errors.answer" class="mt-1 pl-2 text-xs font-medium text-red-500">{{ form.errors.answer }}</div>
                    </div>

                    <div class="my-6 border-t border-dashed border-gray-200"></div>

                    <div class="rounded-2xl bg-orange-50 p-6">
                        <div class="mb-4 flex items-start gap-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="mt-0.5 h-5 w-5 text-[#C85200]"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"
                                />
                            </svg>
                            <div>
                                <label class="block text-sm font-bold text-[#C85200]">Verifikasi Identitas</label>
                                <p class="text-xs text-orange-700/80">
                                    Demi keamanan, masukkan password login Anda saat ini untuk menyimpan perubahan.
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <input
                                v-model="form.current_password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Password Login Saat Ini"
                                class="w-full rounded-xl border-none bg-white px-5 py-3 text-gray-700 placeholder-gray-400 shadow-sm transition-all focus:ring-2 focus:ring-[#C85200] focus:outline-none"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <svg
                                    v-if="!showPassword"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                    />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"
                                    />
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.current_password" class="mt-1 pl-2 text-xs font-medium text-red-500">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 rounded-xl bg-[#0D1B6E] px-8 py-3 font-bold text-white shadow-md shadow-blue-900/20 transition-all hover:bg-blue-900 hover:shadow-lg disabled:opacity-70"
                        >
                            <svg
                                v-if="!form.processing"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
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
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
