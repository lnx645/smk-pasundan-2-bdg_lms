<template>
    <div class="min-h-screen pb-24">
        <div class="mx-auto mt-6 max-w-3xl px-4 sm:px-6">
            <PageTitle :title="`Diskusi`" subtitle="Detail percakapan" />

            <div class="mb-8 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="p-5 sm:p-6">
                    <div class="mb-4 flex items-start justify-between">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-white bg-blue-100 text-lg font-bold text-blue-600 shadow-sm"
                                >
                                    {{ discusion.user.name.charAt(0) }}
                                </div>
                            </div>
                            <div>
                                <h2 class="text-base leading-tight font-bold text-gray-900">
                                    {{ discusion.user.name }}
                                </h2>
                                <div class="mt-1 flex items-center gap-2">
                                    <span
                                        v-if="discusion.matpel_kode"
                                        class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                                    >
                                        {{ discusion.matpel_kode }}
                                    </span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">
                                        {{
                                            new Date(discusion.created_at).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'long',
                                                year: 'numeric',
                                            })
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-sm max-w-none leading-relaxed whitespace-pre-wrap text-gray-800">
                        <div v-html="discusion.description" />
                    </div>

                    <div class="mt-6 flex items-center gap-6 border-t border-gray-50 pt-4 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                ></path>
                            </svg>
                            <span>{{ discusion.likes || 0 }} Suka</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                ></path>
                            </svg>
                            <span>{{ comments.length }} Komentar</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold tracking-wider text-gray-900 uppercase">Komentar</h3>
            </div>

            <div class="min-h-[200px] space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div v-if="comments.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="mb-3 rounded-full bg-gray-50 p-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                            ></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada komentar. Jadilah yang pertama!</p>
                </div>

                <div v-for="c in comments" :key="c.id" class="group flex gap-4 transition-all">
                    <div class="mt-1 flex-shrink-0">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 shadow-sm ring-1 ring-white"
                        >
                            {{ c.user?.name?.charAt(0) || '?' }}
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex flex-col">
                            <div class="flex items-baseline justify-between">
                                <span class="text-sm font-bold text-gray-900">{{ c.user?.name || 'User' }}</span>
                                <span class="text-[11px] text-gray-400">
                                    {{ new Date(c.created_at).toLocaleDateString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm leading-relaxed text-gray-700">{{ c.text }}</p>

                            <!-- <div class="mt-2 flex items-center gap-4">
                                <button class="text-xs font-medium text-gray-500 transition hover:text-blue-600">Suka</button>
                                <button class="text-xs font-medium text-gray-500 transition hover:text-blue-600">Balas</button>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 z-30 w-full border-t border-gray-100 bg-white px-4 py-3 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.05)]">
            <div class="mx-auto flex max-w-3xl items-end gap-3">
                <div class="mb-1 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">
                    {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                </div>

                <div class="relative flex-grow">
                    <textarea
                        v-model="form.text"
                        rows="1"
                        placeholder="Tulis balasan..."
                        class="min-h-[46px] w-full resize-none overflow-hidden rounded-2xl border-gray-200 bg-gray-50 py-3 pr-12 pl-4 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                        @input="autoResize"
                        @keydown.enter.prevent="submitComment"
                    ></textarea>

                    <button
                        @click="submitComment"
                        :disabled="form.processing || !form.text"
                        class="absolute right-2 bottom-2 rounded-full p-1.5 text-blue-600 transition hover:bg-blue-50 disabled:bg-transparent disabled:text-gray-300"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="m22 2-7 20-4-9-9-4Z" />
                            <path d="M22 2 11 13" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import PageTitle from '@/layouts/page-title.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// --- Script Logic sama seperti sebelumnya ---
const page = usePage();
const discusion = computed(() => page.props.discusion as any);

const comments = computed(() => {
    if (page.props.comment) return page.props.comment as any[];
    if (discusion.value?.comments) return discusion.value.comments as any[];
    return [];
});

const form = useForm({
    discusion_id: discusion.value.id,
    text: '',
});

// Fungsi agar textarea nambah tinggi otomatis saat diketik
const autoResize = (event: any) => {
    const element = event.target;
    element.style.height = 'auto';
    element.style.height = element.scrollHeight + 'px';
};

const submitComment = () => {
    if (!form.text.trim()) return;

    const url = DiscusionController.postComment({
        discusion: discusion.value.id,
    }).url;

    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('text');
            // Reset height textarea
            const textarea = document.querySelector('textarea');
            if (textarea) textarea.style.height = 'auto';
        },
    });
};
</script>
