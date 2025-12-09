<template>
    <div class="max-w-7xl mx-auto pb-20">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <PageTitle title="Buat Tugas Baru" subtitle="Atur detail tugas, target siswa, dan materi pembelajaran." />
            <div class="flex items-center gap-3">
                <Button variant="secondary"
                    class="bg-white border border-neutral-300 shadow-sm text-neutral-600 hover:bg-neutral-50">
                    Batal
                </Button>
                <Button @click="simpan" :disabled="form.processing" class="shadow-md shadow-blue-500/20">
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else>Terbitkan Tugas</span>
                </Button>
            </div>
        </div>

        <form @submit.prevent="simpan" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
                    <div class="bg-neutral-50 px-5 py-3 border-b border-neutral-100">
                        <h3 class="font-semibold text-neutral-700 text-sm">Target & Mata Pelajaran</h3>
                    </div>
                    <div class="p-5 space-y-5">

                        <div>
                            <label
                                class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Mata
                                Pelajaran</label>
                            <VueSelect placeholder="Pilih Mapel..." :options="$page.props.matpels" label="nama"
                                :reduce="(item: any) => item.kode_matpel" v-model="form.matpel" class="v-select-custom"
                                :class="{ 'border-red-500 ring-1 ring-red-500 rounded-lg': form.errors.matpel }" />
                            <p v-if="form.errors.matpel" class="mt-1 text-xs text-red-500 font-medium">{{
                                form.errors.matpel }}</p>
                        </div>

                        <hr class="border-dashed border-neutral-200">

                        <div>
                            <label
                                class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Tugaskan
                                Kepada</label>
                            <div class="grid grid-cols-2 bg-neutral-100 p-1 rounded-lg mb-3">
                                <button type="button" @click="form.receiver_type = 'class_id'"
                                    class="text-sm font-medium py-1.5 rounded-md transition-all duration-200"
                                    :class="form.receiver_type === 'class_id' ? 'bg-white text-blue-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-700'">
                                    Per Kelas
                                </button>
                                <button type="button" @click="form.receiver_type = 'siswa_id'"
                                    class="text-sm font-medium py-1.5 rounded-md transition-all duration-200"
                                    :class="form.receiver_type === 'siswa_id' ? 'bg-white text-blue-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-700'">
                                    Per Siswa
                                </button>
                            </div>

                            <div v-if="form.receiver_type == 'class_id'">
                                <VueSelect placeholder="Pilih Kelas..." :options="receiver_type_id" label="nama_kelas"
                                    :reduce="(item: Kelas) => item.id_kelas" :multiple="true"
                                    v-model="form.receiver_type_id" class="v-select-custom" />
                            </div>
                            <div v-else>
                                <VueSelect @search="onSearchSiswa" placeholder="Ketik nama siswa..."
                                    :options="receiver_type_id" :multiple="true" v-model="form.receiver_type_id"
                                    class="v-select-custom">
                                    <template #no-options>Ketik untuk mencari...</template>
                                    <template #option="option">
                                        <div class="flex flex-col py-1">
                                            <span class="font-bold text-sm text-neutral-800">{{ option.user?.name
                                                }}</span>
                                            <span class="text-xs text-neutral-500">{{ option.kelas?.nama }} • {{
                                                option?.nis }}</span>
                                        </div>
                                    </template>
                                    <template #selected-option="option">
                                        {{ option.user?.name }}
                                    </template>
                                </VueSelect>
                            </div>
                            <p v-if="form.errors.receiver_type_id" class="mt-1 text-xs text-red-500 font-medium">{{
                                form.errors.receiver_type_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
                    <div class="bg-neutral-50 px-5 py-3 border-b border-neutral-100">
                        <h3 class="font-semibold text-neutral-700 text-sm">Pengumpulan</h3>
                    </div>
                    <div class="p-5 space-y-5">

                        <div>
                            <label
                                class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Batas
                                Waktu
                                (Deadline)</label>
                            <VueDatePicker v-model="form.deadline" auto-apply :enable-time-picker="true"
                                input-class-name="dp-custom-input" />
                            <p v-if="form.errors.deadline" class="mt-1 text-xs text-red-500 font-medium">{{
                                form.errors.deadline }}</p>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Format
                                Jawaban</label>
                            <VueSelect class="v-select-custom" v-model="form.mode_pengumpulan"
                                placeholder="Pilih Format..." :options="typePengumpulan" />
                            <p v-if="form.errors.mode_pengumpulan" class="mt-1 text-xs text-red-500 font-medium">{{
                                form.errors.mode_pengumpulan }}</p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">

                    <div class="mb-6">
                        <input type="text" v-model="form.judul" placeholder="Tulis Judul Tugas di sini..."
                            class="w-full text-2xl font-bold text-neutral-800 placeholder-neutral-300 border-none focus:ring-0 focus:outline-none p-0" />
                        <div class="h-1 w-20 bg-blue-500 rounded-full mt-2"></div>
                        <p v-if="form.errors.judul" class="mt-2 text-sm text-red-500 font-medium">{{ form.errors.judul
                            }}</p>
                    </div>

                    <div class="relative">
                        <label
                            class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider mb-2">Instruksi
                            /
                            Deskripsi</label>
                        <div class="prose max-w-none ck-content-wrapper"
                            :class="{ 'ring-1 ring-red-500 rounded-lg': form.errors.deskripsi }">
                            <Ckeditor :editor="ClassicEditor" v-model="form.deskripsi" :config="editorConfig" />
                        </div>
                        <p v-if="form.errors.deskripsi" class="mt-1 text-xs text-red-500 font-medium">{{
                            form.errors.deskripsi }}
                        </p>
                    </div>
                </div>
            </div>

        </form>
    </div>
</template>

<script setup lang="ts">
import PageTitle from '@/layouts/page-title.vue';
import Button from '@/components/button.vue';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import {
    ClassicEditor,
    Autoformat, AutoImage, Autosave, BlockQuote, Bold, CKBox, CKBoxImageEdit, CloudServices, Code, CodeBlock, Emoji, Essentials, FontBackgroundColor, FontColor, FontFamily, FontSize, Heading, Highlight, HorizontalLine, ImageBlock, ImageCaption, ImageInline, ImageInsert, ImageInsertViaUrl, ImageResize, ImageStyle, ImageTextAlternative, ImageToolbar, ImageUpload, Indent, IndentBlock, Italic, Link, LinkImage, List, ListProperties, MediaEmbed, Mention, Paragraph, PasteFromOffice, PictureEditing, Strikethrough, Subscript, Superscript, Table, TableCaption, TableCellProperties, TableColumnResize, TableProperties, TableToolbar, TextTransformation, TodoList, Underline,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
import VueSelect from 'vue-select';
import { debounce } from 'lodash';
import { getKelasByMatpel, getSiswa } from '@/routes/guru';
import { Kelas } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'; // Pastikan CSS datepicker diimport
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { simpanTugas } from '@/routes/guru/tugas';
import { toast } from 'vue-sonner';

const typePengumpulan = ['file', 'text', 'foto'];
const receiver_type_id = ref([]);

const form = useForm({
    matpel: null,
    judul: '',
    deskripsi: '',
    mode_pengumpulan: '',
    deadline: '',
    receiver_type_id: [],
    receiver_type: 'class_id',
});

// LOGIC SAMA PERSIS SEPERTI SEBELUMNYA, HANYA RE-ORGANISASI KODE
watch(
    () => form.matpel,
    async ($e) => {
        if (!$e) return;
        const res = await axios.post(getKelasByMatpel().url, { matpel_kode: $e });
        receiver_type_id.value = res.data ?? [];
        form.reset('receiver_type_id');
    },
);

const editorConfig = computed(() => ({
    licenseKey: 'GPL',
    placeholder: 'Tuliskan instruksi tugas secara detail...',
    height: 500,
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

const onSearchSiswa = debounce(async (search: string, loading: (status: boolean) => void) => {
    if (!search) { loading(false); return; }
    loading(true);
    try {
        const { data } = await axios.get(getSiswa().url, { params: { keywords: search } });
        receiver_type_id.value = data;
    } catch (error) {
        console.error("Gagal mencari siswa:", error);
    } finally {
        loading(false);
    }
}, 500);

watch(() => form.receiver_type, () => {
    receiver_type_id.value = [];
})

function simpan() {
    form.submit(simpanTugas())
}

watch(
    () => [form.hasErrors, form.processing],
    () => {
        if (form.hasErrors && !(form.errors as any).success && !form.processing) {
            toast.error('Gagal! Mohon periksa kembali inputan Anda.');
        } else if ((form.errors as any).success) {
            toast.success((form.errors as any).success);
            form.resetAndClearErrors();
            form.reset();
        }
    },
);
</script>

<style>
@reference 'tailwindcss';
/* CUSTOM STYLING UNTUK VUE-SELECT & DATEPICKER AGAR MATCH DENGAN TAILWIND */

/* Vue Select Customization */
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
    @apply border border-neutral-200 shadow-xl rounded-lg mt-1 text-sm;
}

.v-select-custom .vs__selected {
    @apply bg-blue-50 text-blue-700 border border-blue-100 rounded px-2 py-0.5 text-xs font-medium;
}

.v-select-custom .vs__actions {
    @apply text-neutral-400;
}

/* Datepicker Customization (Jika pakai @vuepic/vue-datepicker) */
.dp-custom-input {
    @apply border-neutral-300 rounded-lg shadow-sm h-[42px] text-sm text-neutral-700 font-sans;
}

.dp-custom-input:focus {
    @apply ring-2 ring-blue-500/20 border-blue-500;
}

/* CKEditor Customization to remove outer borders and blend it */
.ck-content-wrapper .ck.ck-editor__main>.ck-editor__editable {
    @apply min-h-[300px] rounded-b-lg border-neutral-300;
}

.ck-content-wrapper .ck.ck-toolbar {
    @apply border-neutral-300 rounded-t-lg bg-neutral-50;
}

.ck-content-wrapper .ck.ck-editor__main>.ck-editor__editable:focus {
    @apply border-blue-500 ring-1 ring-blue-500;
}
</style>