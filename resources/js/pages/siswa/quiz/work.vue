<script setup>
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController';
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    quiz: Object,
    attempt: Object,
    questions: Array,
});

// State
const currentQuestionIndex = ref(0);
const answers = ref({}); // { question_id: option_id }
const timeLeft = ref(0);
const timerInterval = ref(null);

// Inisialisasi Jawaban Kosong
props.questions.forEach((q) => {
    answers.value[q.id] = null;
});

// Logika Timer
const startTimer = () => {
    // Hitung sisa waktu berdasarkan (waktu mulai + durasi kuis) - waktu sekarang
    const startTime = new Date(props.attempt.started_at).getTime();
    const durationMs = props.quiz.duration * 60 * 1000;
    const endTime = startTime + durationMs;

    timerInterval.value = setInterval(() => {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance < 0) {
            clearInterval(timerInterval.value);
            timeLeft.value = 0;
            submitQuiz(true); // Auto submit jika waktu habis
        } else {
            timeLeft.value = Math.floor(distance / 1000);
        }
    }, 1000);
};

// Format Timer (MM:SS)
const formattedTime = computed(() => {
    const minutes = Math.floor(timeLeft.value / 60);
    const seconds = timeLeft.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

// Navigasi Soal
const currentQuestion = computed(() => props.questions[currentQuestionIndex.value]);
const nextQuestion = () => {
    if (currentQuestionIndex.value < props.questions.length - 1) currentQuestionIndex.value++;
};
const prevQuestion = () => {
    if (currentQuestionIndex.value > 0) currentQuestionIndex.value--;
};
const jumpToQuestion = (index) => {
    currentQuestionIndex.value = index;
};

// Cek apakah soal sudah dijawab (untuk styling navigasi)
const isAnswered = (questionId) => !!answers.value[questionId];

// Submit Logic
const form = useForm({ answers: {} });

const submitQuiz = (auto = false) => {
    if (!auto && !confirm('Apakah Anda yakin ingin menyelesaikan ujian ini?')) return;

    clearInterval(timerInterval.value);
    form.answers = answers.value;

    form.post(QuizSiswaController.submit({ attempt: props.attempt.id }).url, {
        replace: true,
    });
};

onMounted(() => {
    startTimer();
    // Warning kalau mau close tab/refresh
    window.onbeforeunload = () => 'Anda sedang mengerjakan ujian!';
});

onUnmounted(() => {
    clearInterval(timerInterval.value);
    window.onbeforeunload = null;
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 font-sans text-gray-900 selection:bg-indigo-100">
        <header class="sticky top-0 z-50 border-b border-gray-200 bg-white px-4 py-3 shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div>
                    <h1 class="max-w-[200px] truncate text-lg font-bold text-gray-900 sm:max-w-md">{{ quiz.title }}</h1>
                    <p class="text-xs text-gray-500">Soal {{ currentQuestionIndex + 1 }} dari {{ questions.length }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 rounded-lg px-4 py-2 font-mono text-lg font-bold shadow-sm transition-colors"
                        :class="timeLeft < 60 ? 'animate-pulse border border-red-100 bg-red-50 text-red-600' : 'bg-gray-900 text-white'"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ formattedTime }}
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 p-4 lg:grid-cols-12 lg:p-8">
            <div class="lg:order-1 lg:col-span-8">
                <div class="flex min-h-[500px] flex-col justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                    <div>
                        <div class="mb-6 flex items-start gap-4">
                            <span
                                class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-200"
                            >
                                {{ currentQuestionIndex + 1 }}
                            </span>
                            <div class="prose prose-lg max-w-none text-gray-800">
                                <p class="leading-relaxed font-medium whitespace-pre-wrap">{{ currentQuestion.question_text }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 pl-14">
                            <label
                                v-for="(option, index) in currentQuestion.options"
                                :key="option.id"
                                class="group relative flex cursor-pointer items-center rounded-xl border-2 p-4 transition-all hover:bg-gray-50"
                                :class="
                                    answers[currentQuestion.id] === option.id
                                        ? 'border-indigo-600 bg-indigo-50/50 hover:bg-indigo-50'
                                        : 'border-gray-200 hover:border-indigo-200'
                                "
                            >
                                <input
                                    type="radio"
                                    :name="`question_${currentQuestion.id}`"
                                    :value="option.id"
                                    v-model="answers[currentQuestion.id]"
                                    class="peer sr-only"
                                />

                                <div
                                    class="mr-4 flex h-6 w-6 flex-none items-center justify-center rounded-full border-2 transition-all"
                                    :class="
                                        answers[currentQuestion.id] === option.id
                                            ? 'border-indigo-600 bg-indigo-600 text-white'
                                            : 'border-gray-300 bg-white text-transparent group-hover:border-indigo-300'
                                    "
                                >
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>

                                <div class="flex-grow text-base font-medium text-gray-700 transition-colors peer-checked:text-indigo-900">
                                    <span class="mr-2 font-bold">{{ String.fromCharCode(65 + index) }}.</span>
                                    {{ option.option_text }}
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-between border-t border-gray-100 pt-6">
                        <button
                            @click="prevQuestion"
                            :disabled="currentQuestionIndex === 0"
                            class="flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-bold text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Sebelumnya
                        </button>

                        <button
                            v-if="currentQuestionIndex < questions.length - 1"
                            @click="nextQuestion"
                            class="flex items-center gap-2 rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-gray-800"
                        >
                            Selanjutnya
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <button
                            v-else
                            @click="submitQuiz(false)"
                            class="flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-green-700"
                        >
                            Selesai & Kumpulkan
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:order-2 lg:col-span-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold tracking-wider text-gray-500 uppercase">Navigasi Soal</h3>

                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-6 lg:grid-cols-5">
                        <button
                            v-for="(q, index) in questions"
                            :key="q.id"
                            @click="jumpToQuestion(index)"
                            class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-bold transition-all"
                            :class="[
                                currentQuestionIndex === index
                                    ? 'border-2 border-indigo-200 bg-white text-indigo-600 ring-2 ring-indigo-500 ring-offset-2'
                                    : isAnswered(q.id)
                                      ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200'
                                      : 'bg-gray-100 text-gray-500 hover:bg-gray-200',
                            ]"
                        >
                            {{ index + 1 }}
                        </button>
                    </div>

                    <div class="mt-8 space-y-3 border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-indigo-600 shadow-sm"></span>
                            <span class="text-sm font-medium text-gray-600">Sudah dijawab</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full bg-gray-200"></span>
                            <span class="text-sm font-medium text-gray-600">Belum dijawab</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="h-3 w-3 rounded-full border-2 border-indigo-500 bg-white"></span>
                            <span class="text-sm font-medium text-gray-600">Sedang dikerjakan</span>
                        </div>
                    </div>

                    <button
                        @click="submitQuiz(false)"
                        class="mt-6 w-full rounded-xl bg-gray-900 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-gray-800"
                    >
                        Kumpulkan Jawaban
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
