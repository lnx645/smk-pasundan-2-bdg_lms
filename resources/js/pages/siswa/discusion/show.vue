<template>
    <div class="mx-auto min-h-screen max-w-4xl bg-[#fbfbfb] px-4">
        <PageTitle :title="`Forum ${current_matpel_name}`" subtitle="Ruang diskusi, tanya jawab, dan berbagi materi kelas" />
        <div class="mb-8 rounded-xl border border-slate-100 bg-white p-5">
            <div class="flex gap-4">
                <div class="hidden shrink-0 sm:block">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">
                        {{ user.name.substring(0, 2).toUpperCase() }}
                    </div>
                </div>
                <div class="flex-grow">
                    <textarea
                        v-model="form.description"
                        placeholder="Bagikan sesuatu atau ajukan pertanyaan..."
                        class="min-h-[80px] w-full resize-none rounded-lg border-none bg-slate-50 p-3 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0"
                    ></textarea>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-bold tracking-wide uppercase"
                    :class="isGuru ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700'"
                >
                    {{ isGuru ? 'Guru' : 'Siswa' }}
                </span>
                <button
                    @click="submit('forum')"
                    :disabled="form.processing || form.description.length < 3"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-xs font-bold text-white transition-all hover:bg-indigo-700 disabled:opacity-50"
                >
                    <span v-if="form.processing">...</span>
                    <span v-else class="flex items-center gap-2">Kirim <Send :size="14" /></span>
                </button>
            </div>
        </div>

        <div class="space-y-6">
            <div v-for="item in discussions" :key="item.id" class="rounded-xl border border-slate-100 bg-white p-5">
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex gap-3">
                        <div class="shrink-0">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-indigo-900">
                                {{ item.user?.name.substring(0, 2).toUpperCase() }}
                            </div>
                        </div>

                        <div>
                            <div class="text-[13px] leading-snug">
                                <span class="font-bold text-indigo-950">{{ item.user?.name }}</span>
                                <span class="mx-1 text-slate-500">memposting {{ item.object_type === 'materi' ? 'materi' : 'diskusi' }}</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-400">
                                {{ item.created_at_human }}
                            </div>
                        </div>
                    </div>

                    <button
                        v-if="$page.props.auth.user.id == item.user_id || ($page.props.auth.user?.role as any) == 'guru'"
                        @click="onDelete(item.id)"
                        class="text-slate-300 transition-colors hover:text-red-500"
                    >
                        <Trash2 :size="16" />
                    </button>
                </div>

                <div class="mb-4">
                    <p class="mb-4 text-[15px] leading-relaxed whitespace-pre-wrap text-slate-600">
                        {{ isLongText(item.description) && !expandedPosts.includes(item.id) ? getTruncatedText(item.description) : item.description }}
                    </p>
                    <button
                        v-if="isLongText(item.description)"
                        @click="toggleExpand(item.id)"
                        class="mt-2 mb-4 text-sm font-bold text-amber-500 transition-colors hover:text-amber-600"
                    >
                        {{ expandedPosts.includes(item.id) ? 'Sembunyikan' : 'Baca Selengkapnya' }}
                    </button>

                    <div v-if="item.object_type === 'materi' && item.linked_object" class="mt-4">
                        <div
                            class="flex items-center justify-between rounded-lg border border-slate-100 bg-white p-4 transition-shadow hover:shadow-md"
                        >
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-notebook-text"
                                    >
                                        <path d="M2 6h4" />
                                        <path d="M2 10h4" />
                                        <path d="M2 14h4" />
                                        <path d="M2 18h4" />
                                        <rect width="16" height="20" x="4" y="2" rx="2" />
                                        <path d="M9.5 8h5" />
                                        <path d="M9.5 12h5" />
                                        <path d="M9.5 16h5" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h4 class="truncate text-sm font-bold text-slate-900">
                                        {{ item.linked_object.judul || item.linked_object.nama || 'Materi Baru' }}
                                    </h4>
                                    <p class="truncate text-xs font-medium text-indigo-900/80">Klik tombol detail untuk membuka materi</p>
                                </div>
                            </div>

                            <div class="shrink-0 pl-4">
                                <Link
                                    :href="view({ id: item.linked_object.id }).url"
                                    class="inline-flex items-center justify-center rounded-lg bg-orange-400 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-orange-500"
                                >
                                    Lihat Detail
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 border-t border-slate-100 pt-4">
                    <button
                        @click="likePost(item.id)"
                        class="flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-indigo-600"
                    >
                        <Heart :size="20" class="stroke-indigo-500" :class="{ 'fill-indigo-500 text-indigo-500': item.is_liked_by_user }" />
                        <span class="font-semibold text-indigo-500">{{ item.likes || 0 }} Suka</span>
                    </button>

                    <Link
                        :href="
                            DiscusionController.comments({
                                matpels_id: item.matpel_kode,
                                kelas_id: item.kelas_id,
                                discusion: item.id,
                            })
                        "
                        class="flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-indigo-600"
                    >
                        <MessageCircle :size="20" class="stroke-indigo-500" />
                        <span class="font-semibold text-indigo-500">{{ item.comments?.length || 0 }} Komentar</span>
                    </Link>
                </div>
            </div>

            <div v-if="discussions.length === 0" class="py-12 text-center">
                <p class="text-slate-400">Belum ada diskusi yang dimulai.</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

// Icons
import { Heart, MessageCircle, Send, Trash2 } from 'lucide-vue-next';
// Components & Utils
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import PageTitle from '@/layouts/page-title.vue';
import { view } from '@/routes/siswa/materi';

// Props Definition
const props = defineProps<{
    matpels: any;
    discussions: any[];
    kelas_id: any;
    matpel_kode: string;
    current_matpel_name: string;
}>();

// State & Computed
const page = usePage();
const user = computed(() => page.props.auth.user);
const isGuru = computed(() => user.value.role === 'guru');
const current_matpel_name = computed(() => props.matpels?.nama || props.current_matpel_name);
const expandedPosts = ref<number[]>([]);

// Form Setup
const form = useForm({
    description: '',
    object_type: 'forum',
    kelas_id: props.kelas_id,
    matpel_kode: props.matpel_kode,
});

// Actions
const submit = (type: string = 'forum') => {
    form.object_type = type;
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

const likePost = (id: number) => {
    router.post(DiscusionController.like({ discussion: id }).url, {}, { preserveScroll: true });
};

const onDelete = (id: string) => {
    Swal.fire({
        title: 'Hapus Postingan?',
        text: 'Tindakan ini tidak bisa dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(DiscusionController.delete({ discusion: id }).url, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Diskusi berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                },
            });
        }
    });
};

// Text Utilities
const toggleExpand = (id: number) => {
    if (expandedPosts.value.includes(id)) {
        expandedPosts.value = expandedPosts.value.filter((postId) => postId !== id);
    } else {
        expandedPosts.value.push(id);
    }
};

const isLongText = (text: string) => {
    return text && text.trim().split(/\s+/).length > 80;
};

const getTruncatedText = (text: string) => {
    return text ? text.split(/\s+/).slice(0, 80).join(' ') + '...' : '';
};
</script>

<style>
body {
    background-color: #fbfbfb;
}
</style>
