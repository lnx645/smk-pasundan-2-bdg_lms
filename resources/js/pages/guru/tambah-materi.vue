<template>
    <div class="max-w-7xl mx-auto pb-20">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
            <PageTitle title="Buat Materi Baru" subtitle="Bagikan video pembelajaran dan dokumen pendukung untuk siswa." />
            
            <div class="flex items-center gap-3">
                <Button variant="secondary" class="bg-white border border-neutral-300 shadow-sm text-neutral-600 hover:bg-neutral-50" @click="goBack">
                    Batal
                </Button>
                <Button @click="simpan" :disabled="data.processing" class="shadow-md shadow-blue-500/20">
                    <span v-if="data.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Terbitkan Materi
                    </span>
                    <span v-else>Terbitkan Materi</span>
                </Button>
            </div>
        </div>

        <form @submit.prevent="simpan" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-neutral-200  sticky top-6">
                    <div class="bg-neutral-50 rounded-t-xl px-4 py-3 border-b border-neutral-100">
                        <h3 class="font-semibold text-neutral-700 text-sm">Media Pembelajaran</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Youtube Video ID / URL</label>
                            <Input v-model="data.youtube_id" placeholder="Paste link Youtube..." class="text-sm" />
                            <p v-if="data.errors.youtube_id" class="mt-1 text-xs text-red-500">{{ data.errors.youtube_id }}</p>
                        </div>

                        <div v-if="getVideID" class="aspect-video rounded-lg  bg-black shadow-sm border border-neutral-200">
                            <VideoPlayer :yt-id="getVideID" />
                        </div>
                        <div v-else class="aspect-video rounded-lg bg-neutral-100 border border-dashed border-neutral-300 flex flex-col items-center justify-center text-neutral-400 p-4 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-xs">Masukkan ID/Link Youtube untuk preview</span>
                        </div>

                        <div>
                            <LinkMateriInputField v-model="data.file_materi" />
                            <p class="text-[10px] text-neutral-400 mt-1 italic">*Maksimal 10 link materi</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-2">
                <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 min-h-[500px]">
                    
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Judul Materi</label>
                        <input 
                            type="text" 
                            v-model="data.title" 
                            placeholder="Contoh: Pengenalan Aljabar Linear" 
                            class="w-full text-xl font-bold text-neutral-800 placeholder-neutral-300 border-b-2 border-transparent focus:border-blue-500 focus:outline-none py-2 transition-colors bg-transparent"
                        />
                        <p v-if="data.errors.title" class="mt-1 text-xs text-red-500">{{ data.errors.title }}</p>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Deskripsi & Instruksi</label>
                        <div class="prose max-w-none ck-content-wrapper" :class="{ 'ring-1 ring-red-500 rounded-lg': data.errors.description }">
                            <Ckeditor :editor="ClassicEditor" v-model="data.description" :config="editorConfig" />
                        </div>
                        <p v-if="data.errors.description" class="mt-1 text-xs text-red-500">{{ data.errors.description }}</p>
                    </div>

                </div>
            </div>

            <div class="lg:col-span-3 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-neutral-200 ">
                    <div class="bg-neutral-50 px-4 py-3 border-b rounded-t-xl border-neutral-100">
                        <h3 class="font-semibold text-neutral-700 text-sm">Target Distribusi</h3>
                    </div>
                    <div class="p-4 space-y-5">
                        
                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                            <VueSelect
                                placeholder="Pilih Matpel..."
                                v-model="data.matpel"
                                :options="$page.props.matpels"
                                :reduce="(item: any) => item" 
                                label="nama"
                                class="v-select-custom"
                                :class="{ 'border-red-500 ring-1 ring-red-500 rounded-lg': data.errors.matpel }"
                            />
                             <p v-if="data.errors.matpel" class="mt-1 text-xs text-red-500">{{ data.errors.matpel }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Kelas Tujuan</label>
                            <VueSelect
                                placeholder="Pilih satu atau lebih..."
                                v-model="data.kelas_ids"
                                :options="$page.props.kelas"
                                :reduce="(item: any) => item.id_kelas"
                                label="nama_kelas"
                                :multiple="true"
                                class="v-select-custom"
                                :class="{ 'border-red-500 ring-1 ring-red-500 rounded-lg': data.errors.kelas_ids }"
                            />
                            <p v-if="data.errors.kelas_ids" class="mt-1 text-xs text-red-500">{{ data.errors.kelas_ids }}</p>
                        </div>

                        <hr class="border-dashed border-neutral-200">

                        <div>
                            <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Jadwal Publish</label>
                            <VueDatePicker 
                                v-model="data.publish_date" 
                                auto-apply 
                                :enable-time-picker="true" 
                                placeholder="Sekarang (Langsung)"
                                input-class-name="dp-custom-input" 
                            />
                            <p class="text-[10px] text-neutral-400 mt-1">Kosongkan untuk publish sekarang.</p>
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
import VueSelect from 'vue-select';
import { toast } from 'vue-sonner';
import { computed, watch } from 'vue';

// CKEDITOR IMPORTS (Sama seperti halaman sebelumnya)
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import {
    ClassicEditor, Autoformat, AutoImage, Autosave, BlockQuote, Bold, CKBox, CKBoxImageEdit, CloudServices, Code, CodeBlock, Emoji, Essentials, FontBackgroundColor, FontColor, FontFamily, FontSize, Heading, Highlight, HorizontalLine, ImageBlock, ImageCaption, ImageInline, ImageInsert, ImageInsertViaUrl, ImageResize, ImageStyle, ImageTextAlternative, ImageToolbar, ImageUpload, Indent, IndentBlock, Italic, Link, LinkImage, List, ListProperties, MediaEmbed, Mention, Paragraph, PasteFromOffice, PictureEditing, Strikethrough, Subscript, Superscript, Table, TableCaption, TableCellProperties, TableColumnResize, TableProperties, TableToolbar, TextTransformation, TodoList, Underline,
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
    publish_date?: string,
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
        Autoformat, AutoImage, Autosave, BlockQuote, Bold, CKBox, CKBoxImageEdit, CloudServices, Emoji, Essentials, Subscript, Heading, ImageBlock, ImageCaption, ImageInline, ImageInsert, ImageInsertViaUrl, ImageResize, ImageStyle, ImageTextAlternative, ImageToolbar, ImageUpload, Indent, IndentBlock, Italic, Link, LinkImage, List, ListProperties, MediaEmbed, Mention, Paragraph, PasteFromOffice, PictureEditing, Table, TableCaption, TableCellProperties, TableColumnResize, TableProperties, TableToolbar, TextTransformation, TodoList, Underline, FontBackgroundColor, FontColor, FontFamily, FontSize, Highlight, HorizontalLine, CodeBlock, Strikethrough, Code, Superscript,
    ],
    toolbar: [
        'heading', '|', 'bold', 'italic', 'underline', 'strikethrough', 'code', '|',
        'bulletedList', 'numberedList', 'todoList', '|',
        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
        'link', 'insertImage', 'insertTable', 'blockQuote', 'codeBlock', '|',
        'undo', 'redo'
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
    @apply border border-neutral-300 bg-white rounded-lg py-1 px-1 min-h-[42px] shadow-sm transition-all;
}
.v-select-custom .vs__dropdown-toggle:focus-within {
    @apply ring-2 ring-blue-500/20 border-blue-500;
}
.v-select-custom .vs__search::placeholder {
    @apply text-neutral-400 text-sm;
}
.v-select-custom .vs__dropdown-menu {
    @apply border border-neutral-200 shadow-xl rounded-lg mt-1 text-sm z-50;
}
.v-select-custom .vs__selected {
    @apply bg-blue-50 text-blue-700 border border-blue-100 rounded px-2 py-0.5 text-xs font-medium;
}


.dp-custom-input {
    @apply border-neutral-300 rounded-lg shadow-sm h-[42px] text-sm text-neutral-700 font-sans ;
}
.dp-custom-input:focus {
    @apply ring-2 ring-blue-500/20 border-blue-500 ;
}

.ck-content-wrapper .ck.ck-editor__main>.ck-editor__editable {
    @apply min-h-[400px] rounded-b-lg border-neutral-300 ;
}
.ck-content-wrapper .ck.ck-toolbar {
    @apply border-neutral-300 rounded-t-lg bg-neutral-50 ;
}
.ck-content-wrapper .ck.ck-editor__main>.ck-editor__editable:focus {
    @apply border-blue-500 ring-1 ring-blue-500 ;
}
</style>