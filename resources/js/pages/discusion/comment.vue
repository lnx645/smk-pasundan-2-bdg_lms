<template>
    <PageTitle :title="`Diskusi`" subtitle="Ruang tanya jawab dan diskusi kelas" />

    <div class="min-h-screen pb-32">
        <div class="px-4">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 font-bold text-white shadow-md"
                        >
                            {{ discusion.user.name.charAt(0) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg leading-tight font-bold text-gray-900">{{ discusion.user.name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{
                                            new Date(discusion.created_at).toLocaleDateString('id-ID', {
                                                weekday: 'long',
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })
                                        }}
                                    </p>
                                </div>
                                <span
                                    v-if="discusion.matpel_kode"
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-blue-700/10 ring-inset"
                                >
                                    {{ discusion.matpel_kode }}
                                </span>
                            </div>

                            <div class="mt-4 text-base leading-relaxed font-normal whitespace-pre-wrap text-gray-800">
                                {{ discusion.description }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-6 py-3">
                    <button class="group flex items-center space-x-2 text-sm font-medium text-gray-500 transition-colors hover:text-red-500">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transition-transform group-active:scale-125"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </svg>
                        <span>{{ discusion.likes || 0 }} Suka</span>
                    </button>
                    <div class="flex items-center space-x-1 text-sm text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                            />
                        </svg>
                        <span>{{ comments.length }} Komentar</span>
                    </div>
                </div>
            </div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-50 px-3 text-sm font-medium text-gray-500">Komentar Terbaru</span>
                </div>
            </div>

            <div class="space-y-6">
                <transition-group name="list">
                    <div v-for="c in comments" :key="c.id" class="group flex space-x-4">
                        <div class="flex-shrink-0">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-sm font-bold text-gray-600 ring-2 ring-white"
                            >
                                {{ c.user?.name?.charAt(0) || '?' }}
                            </div>
                        </div>

                        <div class="flex-grow">
                            <div class="relative rounded-2xl rounded-tl-none bg-white px-5 py-3 shadow-sm ring-1 ring-gray-900/5">
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900">{{ c.user?.name || 'Pengguna Tidak Dikenal' }}</span>
                                    <span class="text-xs text-gray-400">{{
                                        new Date(c.created_at).toLocaleString('id-ID', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            day: 'numeric',
                                            month: 'short',
                                        })
                                    }}</span>
                                </div>
                                <p class="text-sm leading-relaxed text-gray-700">{{ c.text }}</p>
                            </div>
                        </div>
                    </div>
                </transition-group>

                <div v-if="comments.length === 0" class="flex flex-col items-center justify-center py-10 text-center opacity-60">
                    <div class="mb-3 rounded-full bg-gray-200 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                            />
                        </svg>
                    </div>
                    <p class="font-medium text-gray-500">Belum ada komentar.</p>
                    <p class="text-sm text-gray-400">Jadilah yang pertama menanggapi diskusi ini!</p>
                </div>
            </div>
        </div>

        <div
            class="fixed bottom-0 left-0 z-20 w-full border-t border-gray-200 bg-white/80 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] backdrop-blur-md"
        >
            <div class="mx-auto flex max-w-3xl items-center space-x-3">
                <div class="relative flex-grow">
                    <input
                        v-model="form.text"
                        type="text"
                        placeholder="Tulis balasan Anda..."
                        class="w-full rounded-full border-gray-300 bg-gray-50 py-3 pr-12 pl-5 text-sm shadow-sm transition-all focus:border-blue-500 focus:bg-white focus:ring-blue-500"
                        @keyup.enter="submitComment"
                    />
                    <div v-if="form.processing" class="absolute top-3 right-4 text-blue-500">
                        <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                    </div>
                </div>

                <button
                    @click="submitComment"
                    :disabled="form.processing || !form.text"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg transition-all hover:scale-105 hover:bg-blue-700 hover:shadow-blue-500/30 disabled:opacity-50 disabled:hover:scale-100"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import PageTitle from '@/layouts/page-title.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const submitComment = () => {
    if (!form.text.trim()) return;

    const url = DiscusionController.postComment({
        discusion: discusion.value.id,
    }).url;

    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('text');
        },
    });
};
</script>

<style scoped>
/* Animasi halus untuk list komentar */
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
