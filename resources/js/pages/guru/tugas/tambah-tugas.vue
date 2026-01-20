<template>
    <div class="mx-auto max-w-7xl pb-20">
        <div class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <PageTitle title="Buat Tugas Baru" subtitle="Atur detail tugas, target siswa, dan materi pembelajaran." />
            <div class="flex items-center gap-3">
                <Button variant="secondary" class="border border-neutral-300 bg-white text-neutral-600 shadow-sm hover:bg-neutral-50"> Batal </Button>
                <Button @click="simpan" :disabled="form.processing" class="shadow-md shadow-blue-500/20">
                    <span v-if="form.processing" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else>Terbitkan Tugas</span>
                </Button>
            </div>
        </div>

        <form @submit.prevent="simpan" class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-100 bg-neutral-50 px-5 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Target & Mata Pelajaran</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Mata Pelajaran</label>
                            <VueSelect
                                placeholder="Pilih Mapel..."
                                :options="$page.props.matpels"
                                label="nama"
                                :reduce="(item: any) => item.kode_matpel"
                                v-model="form.matpel"
                                class="v-select-custom"
                                :class="{ 'rounded-lg border-red-500 ring-1 ring-red-500': form.errors.matpel }"
                            />
                            <p v-if="form.errors.matpel" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.matpel }}</p>
                        </div>

                        <hr class="border-dashed border-neutral-200" />
                        <div v-if="form.matpel">
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Tugaskan Kepada</label>
                            <div class="mb-3 grid grid-cols-2 rounded-lg bg-neutral-100 p-1">
                                <button
                                    type="button"
                                    @click="form.receiver_type = 'class_id'"
                                    class="rounded-md py-1.5 text-sm font-medium transition-all duration-200"
                                    :class="
                                        form.receiver_type === 'class_id'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-neutral-500 hover:text-neutral-700'
                                    "
                                >
                                    Per Kelas
                                </button>
                                <button
                                    type="button"
                                    @click="form.receiver_type = 'siswa_id'"
                                    class="rounded-md py-1.5 text-sm font-medium transition-all duration-200"
                                    :class="
                                        form.receiver_type === 'siswa_id'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-neutral-500 hover:text-neutral-700'
                                    "
                                >
                                    Per Siswa
                                </button>
                            </div>

                            <div v-if="form.receiver_type == 'class_id'">
                                <VueSelect
                                    placeholder="Pilih Kelas..."
                                    :options="receiver_type_id"
                                    label="nama_kelas"
                                    :reduce="(item: Kelas) => item.id_kelas"
                                    :multiple="true"
                                    v-model="form.receiver_type_id"
                                    class="v-select-custom"
                                />
                            </div>
                            <div v-else>
                                <VueSelect
                                    class="v-select-custom"
                                    @search="onSearchSiswa"
                                    placeholder="Cari Siswa..."
                                    :filterable="false"
                                    :reduce="(item: any) => item.user.id"
                                    :options="receiver_type_id"
                                    :multiple="true"
                                    v-model="form.receiver_type_id"
                                >
                                    <template #no-options> Ketik nama siswa untuk mencari... </template>
                                    <template #option="option">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold">{{ option.user?.name }}</span>
                                            <span class="text-xs text-gray-500">Kelas: {{ option.kelas?.nama }}</span>
                                            <span class="text-xs text-gray-500">NIS: {{ option?.nis }}</span>
                                        </div>
                                    </template>
                                    <template #selected-option="option">
                                        <div class="text-sm">{{ option.user?.name }} [{{ option.kelas?.nama }}]</div>
                                    </template>
                                </VueSelect>
                            </div>
                            <p v-if="form.errors.receiver_type_id" class="mt-1 text-xs font-medium text-red-500">
                                {{ form.errors.receiver_type_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="border-b border-neutral-100 bg-neutral-50 px-5 py-3">
                        <h3 class="text-sm font-semibold text-neutral-700">Pengumpulan</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Batas Waktu (Deadline)</label>
                            <VueDatePicker
                                :formats="{ input: 'dd.MM.yyyy - HH:mm' }"
                                v-model="form.deadline"
                                auto-apply
                                :enable-time-picker="true"
                                input-class-name="dp-custom-input"
                            />
                            <p v-if="form.errors.deadline" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.deadline }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Format Jawaban</label>
                            <VueSelect
                                class="v-select-custom"
                                v-model="form.mode_pengumpulan"
                                placeholder="Pilih Format..."
                                :options="typePengumpulan"
                            />
                            <p v-if="form.errors.mode_pengumpulan" class="mt-1 text-xs font-medium text-red-500">
                                {{ form.errors.mode_pengumpulan }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4 lg:col-span-2">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="mb-6">
                        <input
                            type="text"
                            v-model="form.judul"
                            placeholder="Tulis Judul Tugas di sini..."
                            class="w-full border-none p-0 text-2xl font-bold text-neutral-800 placeholder-neutral-300 focus:ring-0 focus:outline-none"
                        />
                        <div class="mt-2 h-1 w-20 rounded-full bg-blue-500"></div>
                        <p v-if="form.errors.judul" class="mt-2 text-sm font-medium text-red-500">{{ form.errors.judul }}</p>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-xs font-semibold tracking-wider text-neutral-500 uppercase">Instruksi / Deskripsi</label>
                        <div class="ck-content-wrapper prose max-w-none" :class="{ 'rounded-lg ring-1 ring-red-500': form.errors.deskripsi }">
                            <Ckeditor :editor="ClassicEditor" v-model="form.deskripsi" :config="editorConfig" />
                        </div>
                        <p v-if="form.errors.deskripsi" class="mt-1 text-xs font-medium text-red-500">{{ form.errors.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
import Button from '@/components/button.vue';
import PageTitle from '@/layouts/page-title.vue';
import { getKelasByMatpel, getSiswa } from '@/routes/guru';
import { simpanTugas } from '@/routes/guru/tugas';
import { Kelas } from '@/types';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import { useForm } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'; // Pastikan CSS datepicker diimport
import axios from 'axios';
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
import { debounce } from 'lodash';
import { computed, ref, watch } from 'vue';
import VueSelect from 'vue-select';
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

const onSearchSiswa = debounce(async (search: string, loading: (status: boolean) => void) => {
    if (!search) {
        loading(false);
        return;
    }
    loading(true);
    try {
        const { data } = await axios.get(getSiswa().url, { params: { keywords: search } });
        receiver_type_id.value = data;
    } catch (error) {
        console.error('Gagal mencari siswa:', error);
    } finally {
        loading(false);
    }
}, 500);

watch(
    () => form.receiver_type,
    () => {
        receiver_type_id.value = [];
    },
);

function simpan() {
    form.submit(simpanTugas());
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
    @apply min-h-[42px] rounded-lg border border-neutral-300 bg-white px-1 py-1 shadow-sm transition-all;
}

.v-select-custom .vs__dropdown-toggle:focus-within {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}

.v-select-custom .vs__search::placeholder {
    @apply text-sm text-neutral-400;
}

.v-select-custom .vs__dropdown-menu {
    @apply mt-1 rounded-lg border border-neutral-200 text-sm shadow-xl;
}

.v-select-custom .vs__selected {
    @apply rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700;
}

.v-select-custom .vs__actions {
    @apply text-neutral-400;
}

/* Datepicker Customization (Jika pakai @vuepic/vue-datepicker) */
.dp-custom-input {
    @apply h-[42px] rounded-lg border-neutral-300 font-sans text-sm text-neutral-700 shadow-sm;
}

.dp-custom-input:focus {
    @apply border-blue-500 ring-2 ring-blue-500/20;
}

/* CKEditor Customization to remove outer borders and blend it */
.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable {
    @apply min-h-[300px] rounded-b-lg border-neutral-300;
}

.ck-content-wrapper .ck.ck-toolbar {
    @apply rounded-t-lg border-neutral-300 bg-neutral-50;
}

.ck-content-wrapper .ck.ck-editor__main > .ck-editor__editable:focus {
    @apply border-blue-500 ring-1 ring-blue-500;
}
</style>
