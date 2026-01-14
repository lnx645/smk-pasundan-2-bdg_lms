<script setup>
import GuruQuizQuestionController from '@/actions/App/Http/Controllers/GuruQuizQuestionController';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    quiz: Object,
    questions: Array,
});

const form = useForm({
    question_text: '',
    points: 5,
    options: [
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
    ],
});

const addOption = () => {
    if (form.options.length < 5) form.options.push({ option_text: '', is_correct: false });
};

const removeOption = (index) => {
    if (form.options.length > 2) form.options.splice(index, 1);
};

const setCorrectAnswer = (index) => {
    form.options.forEach((opt) => (opt.is_correct = false));
    form.options[index].is_correct = true;
};

const submit = () => {
    if (!form.options.some((opt) => opt.is_correct)) {
        alert('Pilih kunci jawaban (Klik huruf)!');
        return;
    }
    form.post(GuruQuizQuestionController.store({ quiz: props.quiz.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.options = [
                { option_text: '', is_correct: false },
                { option_text: '', is_correct: false },
                { option_text: '', is_correct: false },
                { option_text: '', is_correct: false },
            ];
        },
    });
};

const deleteQuestion = (id) => {
    if (confirm('Hapus soal ini?')) {
        router.delete(GuruQuizQuestionController.destroy({ question: id }).url, { preserveScroll: true });
    }
};
</script>

<template>
    <div class="mt-7 min-h-screen bg-gray-50 pb-12 font-sans text-sm text-gray-800">
        <div class="sticky top-0 z-50 border-b border-gray-200 bg-white px-4 py-2 shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link href="/guru/quiz" class="rounded p-1 text-gray-500 hover:bg-gray-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </Link>
                    <div>
                        <h1 class="leading-none font-bold text-gray-900">{{ quiz.title }}</h1>
                        <span class="text-xs text-gray-500">KKM: {{ quiz.passing_grade }} &bull; Total Soal: {{ questions.length }}</span>
                    </div>
                </div>
                <Link href="/guru/quiz" class="rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-bold text-white transition hover:bg-gray-800">
                    Selesai
                </Link>
            </div>
        </div>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-4 py-4 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="rounded-xl border border-gray-300 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-4 py-2">
                        <h2 class="text-xs font-bold tracking-wider text-gray-500 uppercase">Input Soal Baru</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-4">
                        <div class="mb-3">
                            <textarea
                                v-model="form.question_text"
                                rows="2"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                placeholder="Tulis pertanyaan..."
                                required
                            ></textarea>
                        </div>

                        <div class="mb-4 flex items-center gap-2">
                            <label class="text-xs font-bold text-gray-500">Bobot:</label>
                            <input
                                v-model="form.points"
                                type="number"
                                class="w-16 rounded-md border border-gray-300 px-2 py-1 text-center text-sm font-bold focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            />
                            <span class="text-xs text-gray-400">Poin</span>
                        </div>

                        <div class="mb-4 space-y-2">
                            <div v-for="(option, index) in form.options" :key="index" class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="setCorrectAnswer(index)"
                                    class="flex h-8 w-8 flex-none items-center justify-center rounded border-b-2 text-xs font-black transition-all active:translate-y-[2px] active:border-b-0"
                                    :class="
                                        option.is_correct
                                            ? 'border-indigo-700 bg-indigo-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-400 hover:bg-gray-50'
                                    "
                                >
                                    {{ String.fromCharCode(65 + index) }}
                                </button>

                                <input
                                    type="text"
                                    v-model="option.option_text"
                                    class="block w-full rounded-md border px-3 py-1.5 text-sm transition-colors focus:ring-1 focus:outline-none"
                                    :class="
                                        option.is_correct
                                            ? 'border-indigo-300 bg-indigo-50 text-indigo-900 focus:border-indigo-500 focus:ring-indigo-500'
                                            : 'border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500'
                                    "
                                    :placeholder="`Opsi ${String.fromCharCode(65 + index)}`"
                                    required
                                />

                                <button
                                    type="button"
                                    @click="removeOption(index)"
                                    v-if="form.options.length > 2"
                                    class="text-gray-300 hover:text-red-500"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button
                                v-if="form.options.length < 5"
                                type="button"
                                @click="addOption"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800"
                            >
                                + Tambah Opsi
                            </button>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-gray-900 px-6 py-2 text-xs font-bold text-white shadow hover:bg-gray-800 disabled:opacity-50"
                            >
                                <span v-if="form.processing">...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-3 lg:col-span-7">
                <div v-if="questions.length === 0" class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-400">
                    <p class="text-xs">Belum ada soal.</p>
                </div>

                <div
                    v-for="(q, qIndex) in questions"
                    :key="q.id"
                    class="group relative rounded-xl border border-gray-200 bg-white p-3 shadow-sm transition-all hover:border-indigo-300"
                >
                    <div class="flex gap-3">
                        <span class="flex h-6 w-6 flex-none items-center justify-center rounded bg-gray-100 text-xs font-bold text-gray-600">
                            {{ questions.length - qIndex }}
                        </span>

                        <div class="w-full">
                            <div class="flex items-start justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">{{ q.question_text }}</h3>
                                <button @click="deleteQuestion(q.id)" class="ml-2 text-gray-300 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <div v-for="(opt, optIndex) in q.options" :key="opt.id" class="flex items-center gap-2 text-xs text-gray-600">
                                    <span
                                        class="flex h-4 w-4 flex-none items-center justify-center rounded border text-[10px] font-bold"
                                        :class="
                                            opt.is_correct ? 'border-green-600 bg-green-500 text-white' : 'border-gray-200 bg-white text-gray-400'
                                        "
                                    >
                                        {{ String.fromCharCode(65 + optIndex) }}
                                    </span>
                                    <span :class="{ 'font-bold text-green-700': opt.is_correct }" class="truncate">{{ opt.option_text }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
