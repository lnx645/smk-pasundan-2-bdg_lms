<script setup>
import GuruQuizController from '@/actions/App/Http/Controllers/GuruQuizController';
import GuruQuizQuestionController from '@/actions/App/Http/Controllers/GuruQuizQuestionController';
import { Head, Link } from '@inertiajs/vue3';

// Menerima props 'quizzes' dari Controller
defineProps({
    quizzes: Object,
});

// Helper sederhana untuk format tanggal
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};
</script>

<template>
    <Head title="Daftar Kuis" />

    <div class="py-6">
        <div class="mb-6 flex flex-col items-center justify-between gap-4 md:flex-row">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Daftar Kuis Saya</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola ujian dan latihan untuk kelas Anda.</p>
            </div>

            <Link
                href="/guru/quiz/create"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Buat Kuis Baru
            </Link>
        </div>

        <div v-if="quizzes.data.length === 0" class="rounded-xl border border-gray-100 bg-white p-12 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Belum ada kuis</h3>
            <p class="mt-2 text-gray-500">Mulai buat kuis pertama Anda untuk menguji siswa.</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="quiz in quizzes.data"
                :key="quiz.id"
                class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
            >
                <div class="flex items-start justify-between border-b border-gray-100 bg-gray-50/50 p-5">
                    <div>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            {{ quiz.matpel?.nama ?? quiz.matpel_kode }}
                        </span>
                        <div class="mt-1 text-xs text-gray-500">
                            Kelas: <span class="font-semibold text-gray-700">{{ quiz.kelas?.nama ?? '-' }}</span>
                        </div>
                    </div>
                    <span
                        :class="[
                            'rounded-md border px-2 py-1 text-xs font-semibold',
                            quiz.is_published ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-100 text-gray-600',
                        ]"
                    >
                        {{ quiz.is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>

                <div class="flex-1 p-5">
                    <h3 class="mb-2 line-clamp-2 text-lg font-bold text-gray-900">
                        {{ quiz.title }}
                    </h3>

                    <div class="mt-4 space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-2 h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            {{ quiz.duration }} Menit
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-2 h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            KKM: {{ quiz.passing_grade }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 border-t border-gray-100 bg-white p-4">
                    <Link
                        :href="GuruQuizController.result({ quiz: quiz.id }).url"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Lihat Hasil
                    </Link>
                    <Link
                        :href="GuruQuizQuestionController.index({ quiz: quiz.id })"
                        class="rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-2 text-center text-sm font-medium text-indigo-700 transition hover:bg-indigo-100"
                    >
                        Kelola Soal
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="quizzes.links.length > 3" class="mt-8 flex justify-center">
            <div class="flex flex-wrap gap-1">
                <Component
                    :is="link.url ? Link : 'span'"
                    v-for="(link, key) in quizzes.links"
                    :key="key"
                    :href="link.url"
                    v-html="link.label"
                    class="rounded-md border px-4 py-2 text-sm transition-colors"
                    :class="{
                        'border-indigo-600 bg-indigo-600 text-white': link.active,
                        'border-gray-300 bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
                        'cursor-not-allowed border-gray-200 text-gray-400': !link.url,
                    }"
                />
            </div>
        </div>
    </div>
</template>
