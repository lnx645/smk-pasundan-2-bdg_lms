<script setup lang="ts">
import TugasController, { deleteTugas, editTugas, periksaTugas } from '@/actions/App/Http/Controllers/Guru/TugasController';
import Button from '@/components/button.vue';
import Input from '@/components/input.vue';
import NotData from '@/components/NotData.vue';
import TugasItem from '@/components/TugasItem.vue';
import MaterialSymbolsAddCircleOutline from '@/icons/MaterialSymbolsAddCircleOutline.vue';
import PageTitle from '@/layouts/page-title.vue';
import { all_tugas } from '@/routes/guru/tugas';
import { router, useForm, usePage } from '@inertiajs/vue3';
import VueSelect from 'vue-select';

// Icons untuk Action Button
import IconPencil from '@/icons/IconPencil.vue';
import IconTrash from '@/icons/IconTrash.vue';
import IconCheck from '@/icons/IconCheck.vue';

interface Tugas {
    tugasID: string;
    title: string;
    deadline: string;
    nama_matpel: string;
}

const page = usePage();

const searchData = useForm<{
    kode_matpel: any;
    keywords?: string;
}>({
    kode_matpel: page.props.search_current_terms?.kode_matpel ?? '',
    keywords: page.props.search_current_terms?.keywords ?? '',
});

async function onSearchButtonSubmit() {
    try {
        searchData.get(
            all_tugas({
                kelas_id: page.props.kelas_id as string,
            }).url,
            { preserveState: true }
        );
    } catch (error) {
        console.log(error);
    }
}

function onTambahButton() {
    router.visit(TugasController.tambah().url)
}
function routeToTugas(id: string) {
    router.visit(periksaTugas({ id }).url)
}
function routeToEditTugas(id: string) {
    router.visit(editTugas({ id }).url)
}
function routeToDeleteTugas(id: string) {
    if (confirm('Apakah anda yakin ingin menghapus tugas ini?')) {
        router.delete(deleteTugas({ id }).url)
    }
}
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <PageTitle :title="'Tugas ' + $page.props.info_kelas.nama"
            :subtitle="`Kelola tugas untuk kelas ${$page.props.info_kelas.nama}`" />
        <Button @click="onTambahButton" variant="primary"
            class="shrink-0 flex items-center gap-2 px-4 py-2 text-sm font-medium shadow-sm hover:shadow-md transition-all">
            <div class="flex text-sm space-x-2 items-center">
                <MaterialSymbolsAddCircleOutline class="w-5 h-5" />
                <span>Buat Tugas Baru</span>
            </div>
        </Button>
    </div>

    <div class="mb-6 bg-white p-4 rounded-xl border border-neutral-200 shadow-sm">
        <form @submit.prevent="onSearchButtonSubmit" class="flex flex-col md:flex-row items-center gap-3">

            <div class="relative w-full md:flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <Input placeholder="Cari judul tugas..." v-model="searchData.keywords"
                    class="w-full pl-9 bg-neutral-50 border-neutral-200 focus:bg-white transition-colors" />
            </div>

            <div v-if="$page.props.matpels.length > 1" class="w-full md:w-64">
                <VueSelect placeholder="Semua Mata Pelajaran" :options="$page.props.matpels" label="nama"
                    index="kode_matpel" :reduce="(item: any) => item.kode_matpel" v-model="searchData.kode_matpel"
                    class="v-select-custom" />
            </div>

            <Button :loading="searchData.processing" type="submit" variant="secondary" class="w-full md:w-auto px-6">
                Filter
            </Button>
        </form>
    </div>

    <div class="space-y-4">

        <template v-if="$page.props.tugas.length > 0">
            <TugasItem v-for="i in $page.props.tugas" :key="i.tugasID" :item="i">
                <template #buttons="item">
                    <div
                        class="flex items-center gap-2 mt-4 sm:mt-0 w-full sm:w-auto border-t sm:border-0 pt-3 sm:pt-0 border-neutral-100">

                        <button @click="routeToTugas(i.tugasID)"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                            title="Periksa Jawaban">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Periksa
                        </button>

                        <button @click="routeToEditTugas(i.tugasID)"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors"
                            title="Edit Tugas">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </button>

                        <button @click="routeToDeleteTugas(i.tugasID)"
                            class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                            title="Hapus Tugas">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>

                    </div>
                </template>
            </TugasItem>
        </template>

        <NotData v-else-if="searchData.keywords || searchData.kode_matpel">
            <template #title>Tugas tidak ditemukan</template>
            <template #description>
                Kami tidak menemukan tugas dengan kata kunci "<span class="font-semibold">{{ searchData.keywords
                    }}</span>"
                <span v-if="searchData.kode_matpel"> pada mata pelajaran terpilih</span>.
                <br>Coba gunakan kata kunci lain atau reset filter.
            </template>
        </NotData>
        <NotData v-else>
            <template #title>Belum Ada Tugas</template>
            <template #description>
                Kelas ini belum memiliki tugas aktif.
                <br>Klik tombol <span class="font-medium text-neutral-900">"Buat Tugas Baru"</span> di pojok kanan atas
                untuk memulai.
            </template>
        </NotData>

    </div>
</template>

<style>
@reference "tailwindcss";
.v-select-custom .vs__dropdown-toggle {
    @apply border-neutral-200 bg-neutral-50 rounded-lg py-1.5 px-1 min-h-[42px];
}

.v-select-custom .vs__search::placeholder {
    @apply text-neutral-400 text-sm;
}

.v-select-custom .vs__dropdown-menu {
    @apply border-neutral-200 shadow-lg rounded-lg mt-1 text-sm;
}

.v-select-custom .vs__selected {
    @apply bg-neutral-200 border-0 rounded text-neutral-700 text-sm;
}
</style>