<script setup lang="ts">
import TugasController, { editTugas, periksaTugas } from '@/actions/App/Http/Controllers/Guru/TugasController';
import Button from '@/components/button.vue';
import Input from '@/components/input.vue';
import NotData from '@/components/NotData.vue';
import TugasItem from '@/components/TugasItem.vue';
import MaterialSymbolsAddCircleOutline from '@/icons/MaterialSymbolsAddCircleOutline.vue';
import PageTitle from '@/layouts/page-title.vue';
import { all_tugas } from '@/routes/guru/tugas';
import { router, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import VueSelect from 'vue-select';

interface Tugas {
    tugasID: string;
    title: string;
    deadline: string;
    nama_matpel: string;
}

const page = usePage();
const tugas = page.props.tugas as Tugas[];

const formatTanggal = (date: string) => {
    return dayjs(date).format('DD MMM YYYY HH:mm');
};
const searchData = useForm<{
    kode_matpel: any;
    keywords?: string;
}>({
    kode_matpel: page.props.search_current_terms.keywords ?? '',
    keywords: page.props.search_current_terms.keywords ?? '',
});
async function onSearchButtonSubmit() {
    try {
        searchData.submit(
            all_tugas({
                kelas_id: page.props.kelas_id as string,
            }),
        );
    } catch (error) {
        console.log(error);
    }
}
function onTambahButton() {
    router.visit(TugasController.tambah().url)
}
function routeToTugas(id: string) {
    router.visit(periksaTugas({
        id,
    }).url)
}
function routeToEditTugas(id: string) {
    router.visit(editTugas({
        id: id
    }).url)
}
</script>

<template>
    <PageTitle :title="'Tugas ' + $page.props.info_kelas.nama"
        :subtitle="`List tugas untuk kelas ${$page.props.info_kelas.nama}. Silakan pilih per matpel.`" />

    <div class="mt-5 space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center">
            <Button @click="onTambahButton" variant="danger" class="text-sm flex items-center space-x-1">
                <MaterialSymbolsAddCircleOutline />
            </Button>
            <form @submit.prevent="onSearchButtonSubmit"
                class="flex min-w-xl items-center justify-center space-x-1 rounded  p-3">
                <Input placeholder="Ketikan Kata Kunci Pencarian!" v-model="searchData.keywords"
                    class="flex-1 bg-white text-xs" />
                <VueSelect v-if="$page.props.matpels.length > 1" placeholder="Pilih Mata Pelajaran"
                    :options="$page.props.matpels" label="nama" index="kode_matpel"
                    :reduce="(item: any) => item.kode_matpel" v-model="searchData.kode_matpel" class="flex-1" />
                <Button :loading="searchData.processing" type="submit" class="text-xs">Search</Button>
            </form>
        </div>
        <TugasItem v-if="tugas.length > 0" v-for="i in tugas" :key="i.tugasID" :item="i">
            <template #buttons="item">
                <div class="flex items-center space-x-2">
                    <Button @click="() => routeToTugas(i.tugasID)" class="w-full px-3 py-1.5 text-xs sm:w-auto"> Periksa
                    </Button>
                    <Button @click="() => routeToEditTugas(i.tugasID)" class="w-full px-3 py-1.5  text-xs sm:w-auto">
                        Edit
                    </Button>
                </div>
            </template>
        </TugasItem>
        <NotData v-else-if="page.props.search_current_terms">
            <template #title>Whoops! Tugas yang anda cari tidak ada</template>
            <template #description>Silahkan cari dengan kata kunci yang lain dan valid</template>
        </NotData>
        <NotData v-else>
            <template #title>Whoops! Belum Ada Tugas Saat Ini</template>
            <template #description>Tugas akan muncul di sini jika guru sudah memberikan instruksi baru.</template>
        </NotData>
    </div>
</template>
