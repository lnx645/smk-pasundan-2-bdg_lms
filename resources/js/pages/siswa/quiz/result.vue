<script setup>
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController';
import { Head, Link } from '@inertiajs/vue3';

// Asumsi: Anda menggunakan Ziggy untuk routing.
// Jika menggunakan helper class custom seperti di kode asli Anda, silakan sesuaikan href-nya.

const props = defineProps({
    quiz: Object,
    attempt: Object,
    stats: Object, // { total_questions, correct_answers, is_passed }
});
</script>

<template>
    <Head title="Hasil Ujian" />

    <div class="flex min-h-screen items-center justify-center px-4 py-8 font-sans text-gray-900">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-900/5">
            <div class="flex items-center justify-center gap-2 py-4 text-white" :class="stats.is_passed ? 'bg-green-600' : 'bg-red-600'">
                <svg v-if="stats.is_passed" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <span class="font-bold tracking-wide uppercase">{{ stats.is_passed ? 'LULUS' : 'TIDAK LULUS' }}</span>
            </div>

            <div class="px-6 py-6 text-center">
                <h3 class="line-clamp-1 text-sm font-medium text-gray-500">{{ quiz.title }}</h3>

                <div class="mt-4 flex items-baseline justify-center gap-1 text-gray-900">
                    <span class="text-6xl font-black tracking-tighter">{{ attempt.score }}</span>
                    <span class="text-lg font-medium text-gray-400">/ 100</span>
                </div>
                <div class="mt-1 text-xs font-medium text-gray-400">KKM: {{ quiz.passing_grade }}</div>

                <div class="mt-6 flex divide-x divide-gray-200 border-t border-b border-gray-100 py-3">
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 uppercase">Benar</div>
                        <div class="font-bold text-green-600">{{ stats.correct_answers }}</div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 uppercase">Total Soal</div>
                        <div class="font-bold text-gray-900">{{ stats.total_questions }}</div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="text-xs text-gray-400 uppercase">Durasi</div>
                        <div class="font-bold text-gray-900">
                            {{ Math.ceil((new Date(attempt.finished_at) - new Date(attempt.started_at)) / 60000) }}m
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-[10px] text-gray-400">
                    Selesai:
                    {{
                        new Date(attempt.finished_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
                    }}
                </p>

                <div class="mt-6">
                    <Link
                        :href="QuizSiswaController.index().url"
                        class="block w-full rounded-lg bg-gray-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-gray-800"
                    >
                        Kembali ke Daftar
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
