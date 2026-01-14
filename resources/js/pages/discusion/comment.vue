<template>
    <div class="mx-auto max-w-3xl px-4 py-4 pb-28">
        <PageTitle :title="`Diskusi`" subtitle="Detail percakapan" />

        <div class="mt-6 bg-white">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                    {{ discusion.user.name.charAt(0) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ discusion.user.name }}</h3>
                    <p class="text-xs text-slate-500">
                        {{ new Date(discusion.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </p>
                </div>
                <span v-if="discusion.matpel_kode" class="ml-auto rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                    {{ discusion.matpel_kode }}
                </span>
            </div>

            <div class="text-[15px] leading-relaxed whitespace-pre-wrap text-slate-800">
                {{ discusion.description }}
            </div>

            <div class="mt-4 flex items-center gap-6 border-b border-slate-100 pb-4 text-sm text-slate-500">
                <div class="flex items-center gap-1">
                    <span>{{ discusion.likes || 0 }}</span> Suka
                </div>
                <div class="flex items-center gap-1">
                    <span>{{ comments.length }}</span> Komentar
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            <h4 class="text-sm font-bold text-slate-900">Komentar</h4>

            <div v-for="c in comments" :key="c.id" class="flex gap-3">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600">
                        {{ c.user?.name?.charAt(0) || '?' }}
                    </div>
                </div>

                <div class="flex-grow">
                    <div class="flex items-baseline justify-between">
                        <span class="text-sm font-bold text-slate-900">{{ c.user?.name || 'User' }} ({{ c.user?.role || '?' }})</span>
                        <span class="text-[10px] text-slate-400">{{ new Date(c.created_at).toLocaleDateString() }}</span>
                    </div>
                    <p class="mt-0.5 text-sm leading-relaxed text-slate-600">{{ c.text }}</p>
                </div>
            </div>

            <div v-if="comments.length === 0" class="py-8 text-center text-sm text-slate-400 italic">Belum ada komentar.</div>
        </div>

        <div class="fixed bottom-0 left-0 w-full border-t border-slate-200 bg-white p-3">
            <div class="mx-auto flex max-w-3xl items-center gap-2">
                <input
                    v-model="form.text"
                    type="text"
                    placeholder="Tulis komentar..."
                    class="flex-1 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    @keyup.enter="submitComment"
                />
                <button
                    @click="submitComment"
                    :disabled="form.processing || !form.text"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                    Kirim
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
