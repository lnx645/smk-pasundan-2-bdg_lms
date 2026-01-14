<script setup>
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController';
import { ChartBarIcon, ClockIcon, DocumentTextIcon, InboxIcon, PlayIcon, UserIcon } from '@heroicons/vue/24/outline';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    quizzes: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Daftar Ujian" />

    <div class="min-h-screen py-10 font-sans text-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Daftar Ujian & Kuis</h1>
                    <p class="mt-1 text-sm text-gray-500">Pilih ujian yang tersedia untuk memulai atau lihat riwayat nilai Anda.</p>
                </div>
            </div>

            <div
                v-if="quizzes.length === 0"
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white py-24 text-center"
            >
                <div class="rounded-full bg-gray-50 p-4 ring-1 ring-gray-100">
                    <InboxIcon class="h-10 w-10 text-gray-400" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada ujian aktif</h3>
                <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada ujian yang ditugaskan untuk kelas Anda.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="quiz in quizzes"
                    :key="quiz.id"
                    class="group relative flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all hover:shadow-md hover:ring-indigo-300"
                >
                    <div class="h-1.5 w-full transition-colors" :class="quiz.last_attempt ? 'bg-green-500' : 'bg-indigo-600'"></div>

                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                {{ quiz.matpel?.nama || 'Umum' }}
                            </span>

                            <span
                                v-if="quiz.last_attempt"
                                class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-bold text-green-700 ring-1 ring-green-600/20"
                            >
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 12 12">
                                    <path
                                        d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z"
                                    />
                                </svg>
                                Selesai
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-600/20"
                            >
                                Tersedia
                            </span>
                        </div>

                        <h3 class="mt-4 line-clamp-2 text-lg leading-6 font-bold text-gray-900">
                            {{ quiz.title }}
                        </h3>

                        <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-gray-100 pt-4 text-sm">
                            <div class="flex items-center gap-2 text-gray-500">
                                <ClockIcon class="h-4 w-4 text-gray-400" />
                                <span>{{ quiz.duration }} Menit</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500">
                                <DocumentTextIcon class="h-4 w-4 text-gray-400" />
                                <span>{{ quiz.questions_count }} Soal</span>
                            </div>
                            <div class="col-span-2 flex items-center gap-2 text-gray-500">
                                <UserIcon class="h-4 w-4 text-gray-400" />
                                <span class="truncate">Guru: {{ quiz.guru?.nama || 'Admin' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-5 py-4">
                        <Link
                            v-if="quiz.last_attempt"
                            :href="QuizSiswaController.result({ attempt: quiz.last_attempt.id })"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-all hover:border-green-200 hover:bg-green-50 hover:text-green-700"
                        >
                            <ChartBarIcon class="h-4 w-4" />
                            Lihat Nilai
                        </Link>

                        <Link
                            v-else
                            :href="QuizSiswaController.show({ quiz: quiz.id }).url"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-500 hover:shadow"
                        >
                            <PlayIcon class="h-4 w-4" />
                            Kerjakan Sekarang
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
