<template>
    <div class="mx-auto max-w-7xl pb-20">
        <div class="mb-2 flex flex-col justify-between md:flex-row md:items-center">
            <PageTitle title="Buat Materi Baru" subtitle="Bagikan video pembelajaran dan dokumen pendukung untuk siswa." />

            <div class="flex items-center gap-3">
                <Button variant="secondary" class="border border-neutral-300 bg-white text-neutral-600 shadow-sm hover:bg-neutral-50" @click="goBack">
                    Batal
                </Button>
                <Button @click="simpan" :disabled="data.processing" class="shadow-md shadow-blue-500/20">
                    <span v-if="data.processing" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Terbitkan Materi
                    </span>
                    <span v-else>Terbitkan Materi</span>
                </Button>
            </div>
        </div>

        <form @submit.prevent="simpan" class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-4">
                <div class="sticky top-6 rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="rounded-t-xl border-b border-neutral-100 bg-neutral-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Media Pembelajaran</h3>
                    </div>
                    <div class="space-y-4 p-4">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Youtube Video ID / URL</label>
                            <Input v-model="data.youtube_id" placeholder="Paste link Youtube..." class="text-sm" />
                            <p v-if="data.errors.youtube_id" class="mt-1 text-xs text-red-500">{{ data.errors.youtube_id }}</p>
                        </div>

                        <div v-if="getVideID" class="aspect-video rounded-lg border border-neutral-200 bg-black shadow-sm">
                            <VideoPlayer :yt-id="getVideID" />
                        </div>
                        <div
                            v-else
                            class="flex aspect-video flex-col items-center justify-center rounded-lg border border-dashed border-neutral-300 bg-neutral-100 p-4 text-center text-neutral-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mb-2 h-8 w-8 opacity-50"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs">Masukkan ID/Link Youtube untuk preview</span>
                        </div>

                        <div>
                            <LinkMateriInputField v-model="data.file_materi" />
                            <p class="mt-1 text-[10px] text-neutral-400 italic">*Maksimal 10 link materi</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2 lg:col-span-5">
                <div class="min-h-[500px] rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="mb-6">
                        <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Judul Materi</label>
                        <input
                            type="text"
                            v-model="data.title"
                            placeholder="Contoh: Pengenalan Aljabar Linear"
                            class="w-full border-b-2 border-transparent bg-transparent py-2 text-xl font-bold text-neutral-800 placeholder-neutral-300 transition-colors focus:border-blue-500 focus:outline-none"
                        />
                        <p v-if="data.errors.title" class="mt-1 text-xs text-red-500">{{ data.errors.title }}</p>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Deskripsi & Instruksi</label>
                        <div class="ck-content-wrapper prose max-w-none" :class="{ 'rounded-lg ring-1 ring-red-500': data.errors.description }">
                            <Ckeditor :editor="ClassicEditor" v-model="data.description" :config="editorConfig" />
                        </div>
                        <p v-if="data.errors.description" class="mt-1 text-xs text-red-500">{{ data.errors.description }}</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4 lg:col-span-3">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="rounded-t-xl border-b border-neutral-100 bg-neutral-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Target Distribusi</h3>
                    </div>
                    <div class="space-y-5 p-4">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Mata Pelajaran</label>
                            <VueSelect
                                placeholder="Pilih Matpel..."
                                v-model="data.matpel"
                                :options="$page.props.matpels"
                                :reduce="(item: any) => item"
                                label="nama"
                                class="v-select-custom"
                                :class="{ 'rounded-lg border-red-500 ring-1 ring-red-500': data.errors.matpel }"
                            />
                            <p v-if="data.errors.matpel" class="mt-1 text-xs text-red-500">{{ data.errors.matpel }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Kelas Tujuan</label>
                            <VueSelect
                                placeholder="Pilih satu atau lebih..."
                                v-model="data.kelas_ids"
                                :options="$page.props.kelas"
                                :reduce="(item: any) => item.id_kelas"
                                label="nama_kelas"
                                :multiple="true"
                                class="v-select-custom"
                                :class="{ 'rounded-lg border-red-500 ring-1 ring-red-500': data.errors.kelas_ids }"
                            />
                            <p v-if="data.errors.kelas_ids" class="mt-1 text-xs text-red-500">{{ data.errors.kelas_ids }}</p>
                        </div>

                        <hr class="border-dashed border-neutral-200" />

                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Jadwal Publish</label>
                            <VueDatePicker
                                v-model="data.publish_date"
                                auto-apply
                                :enable-time-picker="true"
                                placeholder="Sekarang (Langsung)"
                                input-class-name="dp-custom-input"
                            />
                            <p class="mt-1 text-[10px] text-neutral-400">Kosongkan untuk publish sekarang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import { simpanMateri } from '@/actions/App/Http/Controllers/GuruMateriController';
import Button from '@/components/button.vue';
import Input from '@/components/input.vue';
import VideoPlayer from '@/components/video-player.vue';
import LinkMateriInputField from '@/features/link-materi-input-field/link-materi-input-field.vue';
import PageTitle from '@/layouts/page-title.vue';
import { getYouTubeVideoId } from '@/lib/utils';
import { useForm, usePage } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { computed, watch } from 'vue';
import VueSelect from 'vue-select';
import { toast } from 'vue-sonner';

// CKEDITOR IMPORTS (Sama seperti halaman sebelumnya)
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import {
    Autoformat,
    AutoImage,
    Autosave,
    BlockQuote,
    Bold,
    CKBox,
    CKBoxImageEdit,
    ClassicEditor,
    CloudServices,
    Code,
    CodeBlock,
    Emoji,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    Heading,
    Highlight,
    HorizontalLine,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    MediaEmbed,
    Mention,
    Paragraph,
    PasteFromOffice,
    PictureEditing,
    Strikethrough,
    Subscript,
    Superscript,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

const page = usePage();
const data = useForm<{
    title?: string;
    youtube_id?: string;
    description?: string;
    kelas_ids?: any[];
    matpel?: any;
    file_materi: string[];
    publish_date?: string;
}>({
    title: '',
    publish_date: '',
    youtube_id: '',
    description: '',
    kelas_ids: page.props.active_kelas as string[], // Asumsi ini array ID
    matpel: '',
    file_materi: [],
});

// CKEditor Config
const editorConfig = computed(() => ({
    licenseKey: 'GPL',
    placeholder: 'Tuliskan deskripsi materi, ringkasan, atau instruksi...',
    height: 400,
    plugins: [
        Autoformat,
        AutoImage,
        Autosave,
        BlockQuote,
        Bold,
        CKBox,
        CKBoxImageEdit,
        CloudServices,
        Emoji,
        Essentials,
        Subscript,
        Heading,
        ImageBlock,
        ImageCaption,
        ImageInline,
        ImageInsert,
        ImageInsertViaUrl,
        ImageResize,
        ImageStyle,
        ImageTextAlternative,
        ImageToolbar,
        ImageUpload,
        Indent,
        IndentBlock,
        Italic,
        Link,
        LinkImage,
        List,
        ListProperties,
        MediaEmbed,
        Mention,
        Paragraph,
        PasteFromOffice,
        PictureEditing,
        Table,
        TableCaption,
        TableCellProperties,
        TableColumnResize,
        TableProperties,
        TableToolbar,
        TextTransformation,
        TodoList,
        Underline,
        FontBackgroundColor,
        FontColor,
        FontFamily,
        FontSize,
        Highlight,
        HorizontalLine,
        CodeBlock,
        Strikethrough,
        Code,
        Superscript,
    ],
    toolbar: [
        'heading',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'code',
        '|',
        'bulletedList',
        'numberedList',
        'todoList',
        '|',
        'fontSize',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'link',
        'insertImage',
        'insertTable',
        'blockQuote',
        'codeBlock',
        '|',
        'undo',
        'redo',
    ],
}));

const getVideID = computed(() => {
    return getYouTubeVideoId(data.youtube_id as string) as string;
});

async function simpan() {
    await data.submit(simpanMateri({ kelas_kode: page.props.kelas_kode as string }));
}

function goBack() {
    window.history.back();
}

watch(
    () => [data.hasErrors, data.processing],
    () => {
        if (data.hasErrors && !(data.errors as any).success && !data.processing) {
            toast.error('Gagal! Mohon periksa kembali inputan Anda.');
        } else if ((data.errors as any).success) {
            toast.success((data.errors as any).success);
            data.resetAndClearErrors();
            data.reset('description', 'file_materi', 'kelas_ids', 'matpel', 'title', 'youtube_id');
        }
    },
);
</script>

<style>
@reference "tailwindcss";
/* Vue Select */
.v-select-custom .vs__dropdown-toggle {
    @apply min-h-[42px] rounded-lg border border-neutral-300 bg-white px-1 py-1 shadow-sm transition-all;
}
.v-select-custom .vs__dropdown-toggle:focus-within {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}
.v-select-custom .vs__search::placeholder {
    @apply text-sm text-neutral-400;
}
.v-select-custom .vs__dropdown-menu {
    @apply z-50 mt-1 rounded-lg border border-neutral-200 text-sm shadow-xl;
}
.v-select-custom .vs__selected {
    @apply rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700;
}

.dp-custom-input {
    @apply h-[42px] rounded-lg border-neutral-300 font-sans text-sm text-neutral-700 shadow-sm;
}
.dp-custom-input:focus {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}

.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable {
    @apply min-h-[400px] rounded-b-lg border-neutral-300;
}
.ck-content-wrapper .ck.ck-toolbar {
    @apply rounded-t-lg border-neutral-300 bg-neutral-50;
}
.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable:focus {
    @apply border-blue-500 ring-1 ring-blue-500;
}
</style>
