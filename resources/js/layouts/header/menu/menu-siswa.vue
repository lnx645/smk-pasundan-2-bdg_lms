<script setup lang="ts">
import { showMateri } from '@/actions/App/Http/Controllers/MateriController';
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import MaterialSymbolsMarkdownPaste from '@/icons/MaterialSymbolsMarkdownPaste.vue';
import MaterialSymbolsMenuBookOutlineRounded from '@/icons/MaterialSymbolsMenuBookOutlineRounded.vue';
import SolarSquareAcademicCap2Bold from '@/icons/SolarSquareAcademicCap2Bold.vue';
import MenuItem from './menu-item.vue';

import NilaiController from '@/actions/App/Http/Controllers/Siswa/NilaiController';
import { showTugas } from '@/actions/App/Http/Controllers/TugasSiswaController';
</script>

<template>
    <MenuItem label="Akademik" href="/" :has-dropdown="true" :icon="SolarSquareAcademicCap2Bold">
        <MenuItem label="Materi" :href="showMateri()" :icon="MaterialSymbolsMenuBookOutlineRounded" />
        <MenuItem label="Tugas" :href="showTugas()" :icon="HugeiconsTaskDaily01" />
        <MenuItem label="Nilai" :href="NilaiController()" :icon="MaterialSymbolsMarkdownPaste" />
    </MenuItem>
</template>
