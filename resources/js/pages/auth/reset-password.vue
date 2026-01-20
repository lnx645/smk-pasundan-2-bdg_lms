<script setup>
import LoginController from '@/actions/App/Http/Controllers/LoginController';
import SiswaSecurityController from '@/actions/App/Http/Controllers/SiswaSecurityController';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref } from 'vue';
import Logo from '../../../img/logo.png';
// Import background image agar sama dengan login
import image from '/resources/img/background.png';

// State
const step = ref(1);
const loading = ref(false);
const errorMessage = ref('');
const securityQuestion = ref('');
const showPassword = ref(false);

// Form Data
const form = reactive({
    nis: '',
    answer: '',
    new_password: '',
});

// --- LOGIC ---

// 1. Cek NIS
const checkNis = async () => {
    if (!form.nis) {
        errorMessage.value = 'NIS wajib diisi.';
        return;
    }

    loading.value = true;
    errorMessage.value = '';

    try {
        const url = SiswaSecurityController.checkNisn().url;
        const res = await axios.post(url, { nis: form.nis });

        securityQuestion.value = res.data.question;
        step.value = 2;
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'NIS tidak ditemukan atau terjadi kesalahan.';
    } finally {
        loading.value = false;
    }
};

// 2. Reset Password
const doReset = async () => {
    if (!form.answer || !form.new_password) {
        errorMessage.value = 'Mohon lengkapi jawaban dan password baru.';
        return;
    }

    if (form.new_password.length < 6) {
        errorMessage.value = 'Password minimal 6 karakter.';
        return;
    }

    loading.value = true;
    errorMessage.value = '';

    try {
        const url = SiswaSecurityController.resetPassword().url;
        await axios.post(url, {
            nis: form.nis,
            answer: form.answer,
            new_password: form.new_password,
        });

        alert('Sukses! Password berhasil diubah. Silakan login.');
        window.location.href = '/login';
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Jawaban salah atau gagal mereset.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Reset Password" />

    <div class="relative h-screen w-full overflow-hidden font-sans">
        <div class="absolute inset-0 z-0">
            <img :src="image" alt="Background" class="h-full w-full object-cover object-center" />
        </div>

        <div class="absolute inset-0 z-0 bg-black/50 backdrop-blur-sm"></div>

        <div class="relative z-10 flex h-full w-full flex-col items-center justify-center px-4">
            <div class="mb-6 flex items-center gap-3">
                <img :src="Logo" alt="Logo SMK Pasundan 2" class="h-16 w-auto object-contain drop-shadow-lg" />
                <div class="flex flex-col leading-tight text-white drop-shadow-md">
                    <h1 class="text-xl font-bold tracking-wide">
                        SMK Pasundan
                        <span class="ml-1 text-2xl text-[#C85200]">2</span>
                    </h1>
                    <h2 class="text-xl font-bold tracking-wide">Bandung</h2>
                </div>
            </div>

            <div class="w-full max-w-sm rounded-[20px] bg-white/95 p-8 shadow-2xl backdrop-blur-md">
                <div class="mb-6">
                    <h3 class="inline-block w-full border-b-2 border-gray-300 pb-2 text-lg font-bold text-gray-800">Reset Password</h3>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ step === 1 ? 'Masukkan NIS untuk mencari akun Anda.' : 'Jawab pertanyaan keamanan untuk mereset password.' }}
                    </p>
                </div>

                <div v-if="step === 1">
                    <div class="mb-6">
                        <label class="mb-2 block pl-1 text-sm font-medium text-gray-700"> NIS </label>
                        <input
                            v-model="form.nis"
                            type="text"
                            placeholder="Masukkan NIS"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none"
                            @keyup.enter="checkNis"
                        />
                    </div>

                    <button
                        @click="checkNis"
                        :disabled="loading"
                        class="w-full transform rounded-xl bg-[#0D1B6E] py-3 text-sm font-bold tracking-wider text-white uppercase shadow-lg transition-all hover:-translate-y-0.5 hover:bg-blue-900 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        {{ loading ? 'Mengecek...' : 'Lanjut' }}
                    </button>
                </div>

                <div v-else-if="step === 2">
                    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
                        <p class="mb-1 text-[10px] font-bold tracking-wider text-[#C85200] uppercase">Pertanyaan Keamanan</p>
                        <p class="text-sm font-semibold text-gray-800 italic">"{{ securityQuestion }}"</p>
                    </div>

                    <div class="mb-4">
                        <label class="mb-2 block pl-1 text-sm font-medium text-gray-700"> Jawaban Anda </label>
                        <input
                            v-model="form.answer"
                            type="text"
                            placeholder="Jawab pertanyaan di atas..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none"
                        />
                    </div>

                    <div class="mb-6">
                        <label class="mb-2 block pl-1 text-sm font-medium text-gray-700"> Password Baru </label>
                        <div class="relative">
                            <input
                                v-model="form.new_password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Minimal 6 karakter"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none"
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
                    </div>

                    <div class="flex flex-col gap-3">
                        <button
                            @click="doReset"
                            :disabled="loading"
                            class="w-full transform rounded-xl bg-[#0D1B6E] py-3 text-sm font-bold tracking-wider text-white uppercase shadow-lg transition-all hover:-translate-y-0.5 hover:bg-blue-900 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            {{ loading ? 'Memproses...' : 'Ubah Password' }}
                        </button>

                        <button
                            @click="step = 1"
                            class="text-center text-xs font-medium text-gray-500 underline transition-colors hover:text-[#0D1B6E]"
                        >
                            Kembali ke Cek NIS
                        </button>
                    </div>
                </div>

                <div v-if="errorMessage" class="mt-4 rounded-lg border border-red-100 bg-red-50 p-3 text-center text-xs font-semibold text-red-500">
                    {{ errorMessage }}
                </div>

                <div class="mt-6 flex justify-center border-t border-gray-200 pt-4">
                    <Link
                        :href="LoginController().url"
                        class="group flex items-center text-xs font-bold text-gray-500 transition-colors hover:text-[#0D1B6E]"
                    >
                        <span class="mr-1 transition-transform group-hover:-translate-x-1">←</span> Kembali ke Login
                    </Link>
                </div>
            </div>

            <div class="mt-8 text-xs text-white/60">&copy; {{ new Date().getFullYear() }} SMK Pasundan 2 Bandung</div>
        </div>
    </div>
</template>
