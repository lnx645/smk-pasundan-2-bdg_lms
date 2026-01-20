<script setup>
import SiswaSecurityController from '@/actions/App/Http/Controllers/SiswaSecurityController';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Logo from '../../../img/logo.png';
// Pastikan path image ini benar sesuai struktur project Laravel/Vite kamu
import image from '/resources/img/background.png';

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login LMS" />

    <div class="relative h-screen w-full overflow-hidden font-sans">
        <div class="absolute inset-0 z-0">
            <img :src="image" alt="Background" class="h-full w-full object-cover object-center" />
        </div>

        <div class="absolute inset-0 z-0 bg-black/50 backdrop-blur-[4px]"></div>

        <div class="relative z-10 flex h-full w-full flex-col items-center justify-center px-4">
            <div class="animate-fade-in-down mb-6 flex items-center gap-3">
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
                    <h3 class="inline-block w-full border-b-2 border-gray-300 pb-2 text-lg font-bold text-gray-800">Learning Management System</h3>
                </div>

                <form @submit.prevent="submit">
                    <div class="mb-5">
                        <label for="email" class="mb-2 block pl-1 text-sm font-medium text-gray-700"> Email / NISN </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="text"
                            placeholder="Masukkan Email atau NISN"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none"
                            :class="{ 'bg-red-50 ring-2 ring-red-500': form.errors.email }"
                        />
                        <div v-if="form.errors.email" class="mt-1 pl-2 text-xs font-medium text-red-500">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="mb-2 block pl-1 text-sm font-medium text-gray-700"> Password </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Password"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none"
                                :class="{ 'bg-red-50 ring-2 ring-red-500': form.errors.password }"
                            />

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 transition-colors hover:text-gray-600 focus:outline-none"
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

                        <div v-if="form.errors.password" class="mt-1 pl-2 text-xs font-medium text-red-500">
                            {{ form.errors.password }}
                        </div>

                        <div class="mt-2 flex justify-end">
                            <Link
                                :href="SiswaSecurityController.index().url"
                                class="text-xs font-semibold text-gray-500 transition-colors hover:text-[#0D1B6E]"
                            >
                                Lupa Password?
                            </Link>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full transform rounded-xl bg-[#0D1B6E] py-3 text-sm font-bold tracking-wider text-white uppercase shadow-lg transition-all hover:-translate-y-0.5 hover:bg-blue-900 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        Sign In
                    </button>
                </form>
            </div>

            <div class="mt-8 text-xs text-white/60">&copy; {{ new Date().getFullYear() }} SMK Pasundan 2 Bandung</div>
        </div>
    </div>
</template>
<style scoped>
body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    overflow: hidden;
}
</style>
