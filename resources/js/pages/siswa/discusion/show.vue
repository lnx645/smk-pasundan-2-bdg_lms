<script setup lang="ts">
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';

// Icons
import { ArrowRight, FileText, Heart, MessageSquare, Send, Trash2 } from 'lucide-vue-next';

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
        title: 'Hapus Diskusi?',
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

<template>
    <div class="mx-auto px-4 py-8">
        <div class="mb-8">
            <PageTitle :title="`Forum ${current_matpel_name}`" subtitle="Ruang diskusi, tanya jawab, dan berbagi materi kelas" />
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-12">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60 transition-shadow hover:shadow-md">
                    <div class="p-4 sm:p-6">
                        <div class="flex gap-4">
                            <div class="hidden sm:block">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white shadow-md"
                                >
                                    {{ user.name.substring(0, 2).toUpperCase() }}
                                </div>
                            </div>
                            <div class="flex-grow">
                                <textarea
                                    v-model="form.description"
                                    placeholder="Bagikan sesuatu atau ajukan pertanyaan..."
                                    class="min-h-[100px] w-full resize-none rounded-xl border-none bg-slate-50 px-4 py-3 text-sm text-slate-700 transition-all placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-6">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                :class="
                                    isGuru ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                "
                            >
                                {{ isGuru ? 'Guru' : 'Siswa' }}
                            </span>
                        </div>
                        <button
                            @click="submit('forum')"
                            :disabled="form.processing || form.description.length < 3"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-xs font-bold text-white transition-all hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 active:scale-95 disabled:pointer-events-none disabled:opacity-50"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else class="flex items-center gap-2"> Kirim <Send :size="14" /> </span>
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        v-for="item in discussions"
                        :key="item.id"
                        class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 transition-all hover:shadow-md hover:ring-slate-300"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex gap-3">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-sm font-bold text-slate-700 shadow-sm ring-2 ring-white"
                                >
                                    {{ item.user?.name.substring(0, 2).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900">{{ item.user?.name }}</h4>
                                        <span
                                            v-if="item.user?.role === 'guru'"
                                            class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset"
                                            >GURU</span
                                        >
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-xs text-slate-500">
                                        <span>{{ item.created_at_human }}</span>
                                        <span>•</span>
                                        <span class="rounded bg-indigo-50 px-1.5 font-medium text-indigo-600">{{ current_matpel_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <button
                                v-if="$page.props.auth.user.id == item.user_id || ($page.props.auth.user?.role as any) == 'guru'"
                                @click="onDelete(item.id)"
                                class="rounded-full p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500"
                                title="Hapus"
                            >
                                <Trash2 :size="16" />
                            </button>
                        </div>

                        <div class="mb-4 pl-14">
                            <div class="text-[14px] leading-7 font-normal whitespace-pre-wrap text-slate-700">
                                {{
                                    isLongText(item.description) && !expandedPosts.includes(item.id)
                                        ? getTruncatedText(item.description)
                                        : item.description
                                }}
                            </div>

                            <button
                                v-if="isLongText(item.description)"
                                @click="toggleExpand(item.id)"
                                class="mt-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                            >
                                {{ expandedPosts.includes(item.id) ? 'Sembunyikan' : 'Baca Selengkapnya' }}
                            </button>

                            <div v-if="item.object_type === 'materi' && item.linked_object" class="mt-4">
                                <Link
                                    :href="view({ id: item.linked_object.id }).url"
                                    class="group/card flex items-center gap-4 rounded-xl border border-indigo-100 bg-indigo-50/50 p-3 transition-all hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-sm"
                                >
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100 transition-transform group-hover/card:scale-105"
                                    >
                                        <FileText :size="24" stroke-width="1.5" />
                                    </div>
                                    <div class="flex-grow overflow-hidden">
                                        <h4 class="truncate text-sm font-bold text-slate-800 transition-colors group-hover/card:text-indigo-700">
                                            {{ item.linked_object.judul || item.linked_object.nama || 'Materi Pembelajaran' }}
                                        </h4>
                                        <div class="mt-0.5 flex items-center gap-2 text-xs text-slate-500">
                                            <span class="text-[10px] font-semibold tracking-wider text-indigo-500 uppercase">Materi</span>
                                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                            <span>Klik untuk membuka</span>
                                        </div>
                                    </div>
                                    <div class="pr-2 text-indigo-300 transition-all group-hover/card:translate-x-1 group-hover/card:text-indigo-600">
                                        <ArrowRight :size="20" />
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 border-t border-slate-100 pt-4 pl-14">
                            <button
                                @click="likePost(item.id)"
                                class="group/btn flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-pink-600"
                            >
                                <div class="rounded-full p-1.5 transition-colors group-hover/btn:bg-pink-50">
                                    <Heart
                                        :size="18"
                                        :class="{ 'fill-pink-500 text-pink-500': item.is_liked_by_user }"
                                        class="transition-transform group-active/btn:scale-125"
                                    />
                                </div>
                                <span>{{ item.likes || 0 }} Suka</span>
                            </button>

                            <Link
                                :href="
                                    DiscusionController.comments({
                                        matpels_id: item.matpel_kode,
                                        kelas_id: item.kelas_id,
                                        discusion: item.id,
                                    })
                                "
                                class="group/btn flex items-center gap-2 text-xs font-medium text-slate-500 transition hover:text-indigo-600"
                            >
                                <div class="rounded-full p-1.5 transition-colors group-hover/btn:bg-indigo-50">
                                    <MessageSquare :size="18" />
                                </div>
                                <span>{{ item.comments?.length || 0 }} Komentar</span>
                            </Link>
                        </div>
                    </div>

                    <div
                        v-if="discussions.length === 0"
                        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center"
                    >
                        <div class="mb-4 rounded-full bg-slate-50 p-4 ring-1 ring-slate-100">
                            <MessageSquare :size="40" class="text-slate-300" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Belum ada diskusi</h3>
                        <p class="mt-1 max-w-sm text-sm leading-relaxed text-slate-500">
                            Jadilah yang pertama memulai percakapan atau bertanya mengenai materi
                            <span class="font-medium text-indigo-600">{{ current_matpel_name }}</span
                            >.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
