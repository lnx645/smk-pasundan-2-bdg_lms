<script setup lang="ts">
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import MaterialSymbolsMarkdownPaste from '@/icons/MaterialSymbolsMarkdownPaste.vue';
import MaterialSymbolsMenuBookOutlineRounded from '@/icons/MaterialSymbolsMenuBookOutlineRounded.vue';
import SolarSquareAcademicCap2Bold from '@/icons/SolarSquareAcademicCap2Bold.vue';
import MenuItem from './menu-item.vue';
import  { materi } from '@/actions/App/Http/Controllers/GuruMateriController';
import { tugas } from '@/routes/guru';
import { kelola_nilai } from '@/actions/App/Http/Controllers/Guru/NilaiController';
</script>

<template>
    <MenuItem label="Manajemen Mapel" href="/" :has-dropdown="true" :icon="SolarSquareAcademicCap2Bold">
        <MenuItem label="Materi" :href="materi()" :icon="MaterialSymbolsMenuBookOutlineRounded" />
        <MenuItem label="Tugas" :href="tugas()" :icon="HugeiconsTaskDaily01" />
        <MenuItem label="Nilai" :href="kelola_nilai()" :icon="MaterialSymbolsMarkdownPaste" />
    </MenuItem>
</template>
