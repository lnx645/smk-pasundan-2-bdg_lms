<script setup lang="ts">
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import PageTitle from '@/layouts/page-title.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const props = defineProps<{
    matpels: any;
    discussions: any[];
    kelas_id: any;
    matpel_kode: string; // Pastikan ini isinya kode (misal: P009)
    current_matpel_name: string;
}>();
const current_matpel_name = computed(() => props.matpels.nama);
const user = computed(() => page.props.auth.user);
const isGuru = computed(() => user.value.role === 'guru');

const form = useForm({
    description: '',
    object_type: 'forum',
    kelas_id: props.kelas_id,
    matpel_kode: props.matpel_kode, // Ini kunci supaya nggak error constraint lagi
});

const submit = (type: string = 'forum') => {
    form.object_type = type;
    // Gunakan route name biasa supaya lebih aman dan terbaca
    form.post(
        DiscusionController.store({
            kelas_id: props.kelas_id,
            matpels_id: props.matpel_kode,
        }).url,
        {
            preserveScroll: true,
            onSuccess: () => form.reset('description'),
        },
    );
};

const expandedPosts = ref<number[]>([]);

// Fungsi Toggle Buka/Tutup
const toggleExpand = (id: number) => {
    if (expandedPosts.value.includes(id)) {
        expandedPosts.value = expandedPosts.value.filter((postId) => postId !== id);
    } else {
        expandedPosts.value.push(id);
    }
};

const isLongText = (text: string) => {
    return text.trim().split(/\s+/).length > 100;
};

const getTruncatedText = (text: string) => {
    return text.split(/\s+/).slice(0, 100).join(' ') + '...';
};
const likePost = (id: number) => {
    router.post(
        DiscusionController.like({
            discussion: id,
        }).url,
        {},
        {
            preserveScroll: true, // Agar halaman tidak loncat ke atas setelah klik
        },
    );
};
</script>

<template>
    <div class="mx-auto py-6">
        <PageTitle :title="`Diskusi / ${current_matpel_name}`" subtitle="Pilih topik untuk mulai berinteraksi" />
        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <textarea
                v-model="form.description"
                placeholder="Apa yang ingin Anda diskusikan hari ini?"
                class="min-h-[100px] w-full resize-none border-none p-4 text-sm focus:ring-0"
            ></textarea>

            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-3">
                <div v-if="isGuru" class="text-xs font-medium text-slate-400 italic">Mode Guru</div>
                <div v-else class="text-xs font-medium text-slate-400 italic">Mode Siswa</div>
                <button
                    @click="submit('forum')"
                    :disabled="form.processing || form.description.length < 3"
                    class="rounded-lg bg-slate-900 px-5 py-1.5 text-xs font-bold text-white uppercase transition-all active:scale-95 disabled:opacity-50"
                >
                    {{ isGuru ? 'Kirim Forum' : 'Kirim Diskusi' }}
                </button>
            </div>
        </div>

        <div class="mt-8 space-y-6">
            <div v-for="item in discussions" :key="item.id" class="group bg-white">
                <div class="flex gap-3">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-400 ring-1 ring-slate-200"
                    >
                        {{ item.user?.name.substring(0, 2).toUpperCase() }}
                    </div>

                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-x-1.5 text-[13px]">
                            <span class="font-bold text-indigo-900"
                                >{{ item.user?.name }}

                                <span class="border border-neutral-300 bg-neutral-100 px-2 py-0.5 text-xs" v-if="item.user?.role == 'guru'"
                                    >Guru</span
                                >
                            </span>
                            <span class="text-slate-500">memposting {{ item.object_type }}</span>
                            <span class="mx-0.5 font-light text-slate-400">›</span>
                            <span class="cursor-pointer font-bold text-emerald-600 hover:underline">
                                {{ current_matpel_name }}
                            </span>
                        </div>

                        <div class="mt-0.5 mb-2 text-[11px] text-slate-400 lowercase">
                            {{ item.created_at_human }}
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition-colors group-hover:border-slate-300">
                            <p class="text-[14px] leading-relaxed whitespace-pre-wrap text-slate-700">
                                {{
                                    isLongText(item.description) && !expandedPosts.includes(item.id)
                                        ? getTruncatedText(item.description)
                                        : item.description
                                }}
                            </p>

                            <button
                                v-if="isLongText(item.description)"
                                @click="toggleExpand(item.id)"
                                class="mt-2 text-xs font-bold text-amber-500 transition-colors hover:text-amber-600"
                            >
                                {{ expandedPosts.includes(item.id) ? 'Sembunyikan' : 'Baca Selengkapnya...' }}
                            </button>
                            <div class="mt-4 flex gap-5 border-t border-slate-50 pt-3">
                                <button
                                    @click="likePost(item.id)"
                                    class="group/like flex items-center gap-1.5 text-[11px] font-bold text-slate-400 transition-colors hover:text-red-500"
                                >
                                    <span class="inline-block text-base transition-transform group-active/like:scale-125"> ❤️ </span>
                                    {{ item.likes }}
                                </button>
                                <Link class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400 transition-colors hover:text-indigo-600">
                                    <span class="text-base">💬</span> Balas
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
