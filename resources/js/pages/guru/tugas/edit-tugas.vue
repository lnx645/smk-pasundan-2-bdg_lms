<template>
    <PageTitle title="Tambah Tugas" subtitle="Silahkan tambah tugas dengan memilih mata pelajaran dan kelas." />
    <form @submit.prevent="simpan" class="flex flex-col gap-3 rounded-xl bg-white p-6 text-neutral-600 shadow">
        <div class="grid lg:grid-cols-2 gap-2 lg:gap-4">
            <div>
                <label class="mb-1 block text-sm font-normal">Mata Pelajaran</label>
                <VueSelect placeholder="Pilih Mata Pelajaran" :options="$page.props.matpels" label="nama"
                    :reduce="(item: any) => item.kode_matpel" v-model="form.matpel" class="w-full"
                    :class="{ 'border-red-500': form.errors.matpel }" />

                <div v-if="form.errors.matpel" class="mt-1 text-xs text-red-500">
                    {{ form.errors.matpel }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 space-x-2">
            <div>
                <label class="mb-1 block text-sm font-normal">
                    Jenis Penerima
                </label>
                <VueSelect placeholder="Pilih Kelas" :filterable="false" :options="jenisPenerima" label="name"
                    :reduce="(item: any) => item.key" :multiple="false" v-model="form.receiver_type" class="w-full" />

                <div v-if="form.errors.receiver_type" class="mt-1 text-xs text-red-500">
                    {{ form.errors.receiver_type }}
                </div>
            </div>

            <div v-if="form.receiver_type == 'class_id'">
                <label class="mb-1 block text-sm font-normal">Kelas</label>
                <VueSelect placeholder="Pilih Kelas" :filterable="false" :options="receiver_type_id" label="nama_kelas"
                    :reduce="(item: Kelas) => item.id_kelas" :multiple="true" v-model="form.receiver_type_id"
                    class="w-full" />
            </div>
            <div v-else-if="form.receiver_type == 'siswa_id'">
                <label class="mb-1 block text-sm font-normal">Pilih Siswa</label>
                <VueSelect @search="onSearchSiswa" placeholder="Cari Siswa..." :filterable="false"
                    :options="receiver_type_id" :multiple="true" v-model="form.receiver_type_id" class="w-full">
                    <template #no-options>
                        Ketik nama siswa untuk mencari...
                    </template>
                    <template #option="option">
                        <div class="flex flex-col">
                            <span class="font-bold text-sm">{{ option.user?.name }}</span>
                            <span class="text-xs text-gray-500">Kelas: {{ option.kelas?.nama }}</span>
                            <span class="text-xs text-gray-500">NIS: {{ option?.nis }}</span>
                        </div>
                    </template>
                    <template #selected-option="option">
                        <div class="text-sm"> {{ option.user?.name }} [{{ option.kelas?.nama }}]</div>
                    </template>
                </VueSelect>
            </div>

            <div class="col-span-2">
                <div v-if="form.errors.receiver_type_id" class="mt-1 text-xs text-red-500">
                    {{ form.errors.receiver_type_id }}
                </div>
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-normal">Judul Tugas</label>
            <Input placeholder="Judul Tugas" v-model="form.judul" />

            <div v-if="form.errors.judul" class="mt-1 text-xs text-red-500">
                {{ form.errors.judul }}
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-normal">Deskripsi Tugas</label>
            <div :class="{ 'border border-red-500 rounded': form.errors.deskripsi }">
                <Ckeditor class="prose" :editor="ClassicEditor" v-model="form.deskripsi" :config="editorConfig" />
            </div>

            <div v-if="form.errors.deskripsi" class="mt-1 text-xs text-red-500">
                {{ form.errors.deskripsi }}
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-2 lg:gap-4">
            <div>
                <label class="mb-1 block text-sm font-normal">Deadline</label>
                <VueDatePicker v-model="form.deadline" />

                <div v-if="form.errors.deadline" class="mt-1 text-xs text-red-500">
                    {{ form.errors.deadline }}
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-normal">Type Pengumpulan</label>
                <VueSelect class="custom-vselect" v-model="form.mode_pengumpulan" placeholder="Type Pengumpulan"
                    :options="typePengumpulan" />

                <div v-if="form.errors.mode_pengumpulan" class="mt-1 text-xs text-red-500">
                    {{ form.errors.mode_pengumpulan }}
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <Button type="submit" :disabled="form.processing">
                <span v-if="form.processing">MENYIMPAN...</span>
                <span v-else>SIMPAN</span>
            </Button>
            <Button variant="secondary">SAVE DRAFT</Button>
        </div>
    </form>
</template>

<script setup lang="ts">
import Input from '@/components/input.vue';
import PageTitle from '@/layouts/page-title.vue';
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
import VueSelect from 'vue-select';
const typePengumpulan = ['file', 'text', 'foto'];
import { debounce } from 'lodash';
import { getKelasByMatpel, getSiswa } from '@/routes/guru';
import { Kelas } from '@/types';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import Button from '@/components/button.vue';
import { simpanTugas } from '@/routes/guru/tugas';
import { toast } from 'vue-sonner';
import { updateTugas } from '@/actions/App/Http/Controllers/Guru/TugasController';
const props = usePage().props as any;
const receiver_type_id = ref([]);

const form = useForm({
    matpel: props.tugas.matpel_kode,
    judul: props.tugas.title,
    deskripsi: props.tugas.content,
    mode_pengumpulan: props.tugas.mode_pengumpulan,
    deadline: props.tugas.deadline,
    receiver_type: props.tugas.receiver_type,
    receiver_type_id: props.tugas.receiver_type_id ?? []
});

const jenisPenerima = [
    {
        name: "Kelas Terpilih",
        key: 'class_id',
    },
    {
        name: "Siswa Terpilih",
        key: 'siswa_id',
    }
];
watch(
    () => form.matpel,
    async ($e) => {
        if (!$e) return;
        const res = await axios.post(getKelasByMatpel().url, { matpel_kode: $e });

        receiver_type_id.value = res.data ?? [];
        form.reset('receiver_type_id');
    },
);

// CKEditor config
const editorConfig = computed(() => ({
    licenseKey: 'GPL',
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
        'undo',
        'redo',
        '|',
        'heading',
        '|',
        'fontSize',
        'fontFamily',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'subscript',
        'superscript',
        'code',
        '|',
        'emoji',
        'horizontalLine',
        'link',
        'insertImage',
        'ckbox',
        'mediaEmbed',
        'insertTable',
        'highlight',
        'blockQuote',
        'codeBlock',
        '|',
        'bulletedList',
        'numberedList',
        'todoList',
        'outdent',
        'indent',
    ],
}));
const onSearchSiswa = debounce(async (search: string, loading: (status: boolean) => void) => {
    // 1. Jika search kosong, stop loading & return
    if (!search) {
        loading(false);
        return;
    }

    loading(true);

    try {
        const { data } = await axios.get(getSiswa().url, {
            params: { keywords: search } // Kirim parameter
        });

        receiver_type_id.value = data;
    } catch (error) {
        console.error("Gagal mencari siswa:", error);
    } finally {
        // 5. Matikan loading (baik sukses maupun error)
        loading(false);
    }
}, 500); // <-- Delay 500ms (tunggu user berhenti mengetik 0.5 detik)

watch(() => form.receiver_type, () => {
    receiver_type_id.value = [];
})
async function simpan() {
    try {
        await form.submit(updateTugas({
            id: props.tugas.tugasID,
        }))
        router.reload()
    } catch (error) {

    }

}

watch(
    () => [form.hasErrors, form.processing],
    (e) => {
        if (form.hasErrors && !(form.errors as any).success && !form.processing) {
            toast.error('Gagal!Tolong periksa inputan anda lagi!');
        } else if ((form.errors as any).success) {
            toast.success((form.errors as any).success);
            form.resetAndClearErrors();
            form.reset();
        }
    },
);


onMounted(async () => {
    if (form.receiver_type === "class_id") {
        const res = await axios.post(getKelasByMatpel().url, {
            matpel_kode: form.matpel
        });
        receiver_type_id.value = res.data;
    }

    if (form.receiver_type === "siswa_id") {
        // convert receiver_type_id into real siswa data
        const { data } = await axios.get(getSiswa().url, {
            params: { ids: form.receiver_type_id }
        });
        receiver_type_id.value = data;
    }
});
</script>
