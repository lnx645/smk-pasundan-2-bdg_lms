<template>
    <PageTitle title="Tugas" subtitle="List tugas silahkan pilih perkelas" />
    <PilihKelas  :kelas="$page.props.kelas">
        <template #link="slotProps">
            <Link
                :href="index({ kelas_id: slotProps.item.id_kelas })"
                class="mt-3 block rounded-lg bg-orange-500 p-2 text-center text-xs font-semibold text-white shadow"
            >
                Pilih Kelas
            </Link>
        </template>
    </PilihKelas>
</template>
<script setup lang="ts">
import {index} from '@/actions/App/Http/Controllers/Guru/TugasController';
import PilihKelas from '@/components/pilih-kelas.vue';
import PageTitle from '@/layouts/page-title.vue';
import { Link } from '@inertiajs/vue3';
</script>
