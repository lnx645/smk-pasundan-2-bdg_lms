<script setup>
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController';
import { CalendarDaysIcon, CheckBadgeIcon, ClockIcon, DocumentTextIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    quiz: Object,
    has_attempt: Boolean,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
};

const status = computed(() => {
    const now = new Date().getTime();
    const start = new Date(props.quiz.start_date).getTime();
    const end = new Date(props.quiz.end_date).getTime();

    if (now < start) {
        return {
            can_start: false,
            message: 'Belum Dimulai',
            // Badge style
            classes: 'bg-amber-100 text-amber-700 ring-1 ring-amber-600/20',
        };
    }
    if (now > end) {
        return {
            can_start: false,
            message: 'Selesai',
            classes: 'bg-red-100 text-red-700 ring-1 ring-red-600/20',
        };
    }
    return {
        can_start: true,
        message: 'Tersedia',
        classes: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-600/20',
    };
});

const startQuiz = () => {
    if (!status.value.can_start) return;
    if (confirm('Waktu akan berjalan segera setelah Anda menekan OK. Yakin ingin memulai?')) {
        router.post(QuizSiswaController.start({ quiz: props.quiz.id }).url);
    }
};
</script>

<template>
    <Head :title="quiz.title" />

    <div class="flex min-h-screen items-center justify-center px-4 py-10 font-sans text-gray-900">
        <div class="w-full max-w-2xl">
            <div class="mb-4">
                <Link
                    href="/siswa/quiz"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </Link>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="h-1.5 w-full bg-indigo-600"></div>

                <div class="px-6 py-6 sm:px-8 sm:py-8">
                    <div class="flex flex-col-reverse items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                                <span class="text-indigo-600">{{ quiz.matpel?.nama || 'Umum' }}</span>
                                <span>&bull;</span>
                                <span>{{ quiz.guru?.nama || 'Admin' }}</span>
                            </div>
                            <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl">
                                {{ quiz.title }}
                            </h1>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium" :class="status.classes">
                            {{ status.message }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 divide-x divide-gray-100 border-y border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col items-center justify-center p-4 text-center sm:flex-row sm:gap-3">
                        <ClockIcon class="h-5 w-5 text-gray-400" />
                        <div class="text-left">
                            <div class="text-xs font-medium text-gray-500 uppercase">Durasi</div>
                            <div class="font-semibold text-gray-900">{{ quiz.duration }} Menit</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4 text-center sm:flex-row sm:gap-3">
                        <CheckBadgeIcon class="h-5 w-5 text-gray-400" />
                        <div class="text-left">
                            <div class="text-xs font-medium text-gray-500 uppercase">KKM</div>
                            <div class="font-semibold text-gray-900">{{ quiz.passing_grade }}</div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4 text-center sm:flex-row sm:gap-3">
                        <DocumentTextIcon class="h-5 w-5 text-gray-400" />
                        <div class="text-left">
                            <div class="text-xs font-medium text-gray-500 uppercase">Soal</div>
                            <div class="font-semibold text-gray-900">{{ quiz.questions_count ?? '-' }} Butir</div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <div class="prose prose-sm max-w-none prose-gray">
                        <p v-if="quiz.description" class="mb-4 text-gray-600 italic">"{{ quiz.description }}"</p>

                        <h4 class="mb-2 font-bold text-gray-900">Petunjuk Pengerjaan:</h4>
                        <ul class="list-disc space-y-1 pl-4 text-gray-600 marker:text-gray-400">
                            <li>Waktu berjalan otomatis saat tombol <strong>Mulai</strong> ditekan.</li>
                            <li>Pastikan koneksi internet Anda stabil.</li>
                            <li>Jangan me-refresh halaman saat ujian berlangsung.</li>
                            <li>Jawaban tersimpan otomatis.</li>
                        </ul>
                    </div>

                    <div class="mt-6 flex gap-3 rounded-lg bg-orange-50 p-3 ring-1 ring-orange-100">
                        <ExclamationTriangleIcon class="h-5 w-5 flex-shrink-0 text-orange-500" />
                        <p class="text-xs leading-5 text-orange-800">
                            <strong>Perhatian:</strong> Mode layar penuh mungkin akan diaktifkan. Dilarang membuka tab lain.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 bg-gray-50 px-6 py-5 sm:flex-row sm:px-8">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <CalendarDaysIcon class="h-5 w-5 text-gray-400" />
                        <span
                            >Mulai: <span class="font-medium text-gray-900">{{ formatDate(quiz.start_date) }}</span></span
                        >
                    </div>

                    <button
                        @click="startQuiz"
                        :disabled="!status.can_start"
                        class="inline-flex w-full items-center justify-center rounded-lg px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                        :class="
                            status.can_start
                                ? 'bg-indigo-600 hover:bg-indigo-500 hover:shadow focus-visible:outline-indigo-600 active:scale-95'
                                : 'bg-gray-300 text-gray-500'
                        "
                    >
                        {{ status.can_start ? 'Mulai Kerjakan' : status.message }}
                    </button>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">Sistem Ujian Online &copy; 2025 Sekolah</p>
        </div>
    </div>
</template>
