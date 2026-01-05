<script setup lang="ts">
import { formatTanggal } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue'; // Import Lifecycle hooks
// Icons
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController'; // Helper route controller quiz
import HugeiconsTime02 from '@/icons/HugeiconsTime02.vue';
import MaterialSymbolsCheckCircle from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import { kerjakan } from '@/routes/siswa/tugas';

const page = usePage();
const user = computed(() => page.props.auth.user as any);
const pendingTasks = computed(() => (page.props.pending_tugas as any) || []);
const activeQuizzes = computed(() => (page.props.active_quizzes as any) || []); // Ambil data kuis

// --- LOGIKA COUNTDOWN REALTIME ---
const now = ref(new Date());
let timerInterval: any = null;

// Fungsi update waktu sekarang setiap detik
onMounted(() => {
    timerInterval = setInterval(() => {
        now.value = new Date();
    }, 1000);
});

// Bersihkan interval saat pindah halaman agar tidak memory leak
onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

// Helper hitung sisa waktu (Jam:Menit:Detik)
const getCountdown = (endDateString: string) => {
    const end = new Date(endDateString).getTime();
    const current = now.value.getTime();
    const diff = end - current;

    if (diff <= 0) return 'Berakhir';

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    // Format 00:00:00
    const pad = (n: number) => n.toString().padStart(2, '0');

    // Jika lebih dari 24 jam, tampilkan hari
    if (hours > 24) {
        const days = Math.floor(hours / 24);
        return `${days} Hari ${hours % 24} Jam`;
    }

    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
};

// Helper hitung sisa hari tugas (yg lama)
const getDaysLeft = (deadline: string) => {
    const today = new Date();
    const due = new Date(deadline);
    const diff = Math.ceil((due.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    return diff;
};
</script>

<template>
    <div class="mx-auto mt-4 max-w-7xl pb-20">
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-neutral-800">Halo, {{ user.nama }}</h2>
                            <p class="mt-1 text-sm text-neutral-500">Selamat datang kembali di ruang belajar Anda.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded border border-neutral-200 bg-neutral-100 px-3 py-1 text-xs font-semibold tracking-wider text-neutral-600 uppercase"
                            >
                                {{ user.role }}
                            </div>
                            <div v-if="user.kelas" class="rounded border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ user.kelas.nama }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="user.role === 'siswa'" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-neutral-200 bg-white p-4">
                        <p class="text-xs font-medium text-neutral-400 uppercase">Tugas Menunggu</p>
                        <p class="mt-1 text-2xl font-bold text-neutral-800">{{ pendingTasks.length }}</p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-4">
                        <p class="text-xs font-medium text-neutral-400 uppercase">Kuis Aktif</p>
                        <p class="mt-1 text-2xl font-bold text-indigo-600">{{ activeQuizzes.length }}</p>
                    </div>
                </div>
            </div>

            <div v-if="user.role === 'siswa'" class="sticky top-6 space-y-6 lg:col-span-4">
                <div class="overflow-hidden rounded-xl border border-rose-200 bg-white shadow-sm ring-1 ring-rose-100">
                    <div class="flex items-center justify-between border-b border-rose-100 bg-rose-50 px-5 py-3">
                        <h3 class="flex items-center gap-2 font-bold text-rose-800">
                            <span class="relative flex h-3 w-3">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex h-3 w-3 rounded-full bg-rose-500"></span>
                            </span>
                            Ujian Berlangsung
                        </h3>
                    </div>

                    <div v-if="activeQuizzes.length > 0" class="divide-y divide-rose-100">
                        <div v-for="quiz in activeQuizzes" :key="quiz.id" class="p-4 transition-all hover:bg-rose-50/50">
                            <div class="mb-2 flex items-start justify-between">
                                <span class="text-[10px] font-bold tracking-wide text-rose-400 uppercase">
                                    {{ quiz.matpel?.nama }}
                                </span>
                                <span class="rounded bg-rose-100 px-2 py-0.5 font-mono text-xs font-bold text-rose-600">
                                    {{ getCountdown(quiz.end_date) }}
                                </span>
                            </div>

                            <h4 class="mb-3 text-sm font-semibold text-gray-900">
                                {{ quiz.title }}
                            </h4>

                            <Link
                                :href="QuizSiswaController.show({ quiz: quiz.id }).url"
                                class="flex w-full items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700"
                            >
                                Kerjakan Sekarang
                            </Link>
                        </div>
                    </div>

                    <div v-else class="px-6 py-8 text-center">
                        <p class="text-sm text-gray-500">Tidak ada ujian yang sedang aktif.</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                        <h3 class="font-bold text-neutral-800">Tugas Anda</h3>
                        <Link href="/siswa/tugas" class="text-xs font-medium text-neutral-400 transition-colors hover:text-neutral-800">
                            Lihat Semua
                        </Link>
                    </div>

                    <div v-if="pendingTasks.length > 0" class="divide-y divide-neutral-100">
                        <div v-for="task in pendingTasks" :key="task.tugasID" class="group relative p-4 transition-all hover:bg-neutral-50">
                            <div
                                class="absolute top-0 bottom-0 left-0 w-[3px]"
                                :class="getDaysLeft(task.deadline) <= 1 ? 'bg-orange-500' : 'bg-transparent group-hover:bg-blue-500'"
                            ></div>

                            <div class="pl-2">
                                <div class="mb-1 flex items-start justify-between">
                                    <span class="text-[10px] font-bold tracking-wide text-neutral-400 uppercase">
                                        {{ task.matpel?.nama }}
                                    </span>

                                    <span
                                        class="text-[10px] font-medium"
                                        :class="getDaysLeft(task.deadline) <= 1 ? 'text-orange-600' : 'text-neutral-400'"
                                    >
                                        {{ getDaysLeft(task.deadline) <= 0 ? 'Hari Ini' : getDaysLeft(task.deadline) + ' hari lagi' }}
                                    </span>
                                </div>

                                <Link :href="kerjakan({ id: task.tugasID })" class="mb-2 block">
                                    <h4 class="text-sm leading-snug font-semibold text-neutral-800 transition-colors group-hover:text-blue-600">
                                        {{ task.title }}
                                    </h4>
                                </Link>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-xs text-neutral-500">
                                        <HugeiconsTime02 class="h-3 w-3 text-neutral-400" />
                                        <span>{{ formatTanggal(task.deadline) }}</span>
                                    </div>

                                    <Link
                                        :href="kerjakan({ id: task.tugasID })"
                                        class="text-xs font-semibold text-blue-600 opacity-0 transition-opacity group-hover:opacity-100 hover:underline"
                                    >
                                        Kerjakan &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center">
                        <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-neutral-50 text-neutral-300">
                            <MaterialSymbolsCheckCircle class="text-2xl" />
                        </div>
                        <p class="text-sm font-semibold text-neutral-900">Tidak ada tagihan tugas</p>
                        <p class="mt-1 text-xs text-neutral-500">Anda bisa bersantai sejenak.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
